<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Samurai\Component\Migration\Phinx\Adapter;

use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Phinx\Db\Table\Column as PhinxColumn;
use Phinx\Db\Adapter\SQLiteAdapter as PhinxSQLiteAdapter;

/**
 * Phinx sqlite adapter wrapper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SQLiteAdapter extends PhinxSQLiteAdapter
{
    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (isset($options['raikiri'])) {
            $this->setContainer($options['raikiri']);
        }

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->connection) return;

        $onikiri = $this->onikiri();
        $alias = $this->options['alias'];

        $database = $onikiri->getDatabase($alias);
        if (! $database) return parent::connect();

        $connection = $database->connect();
        $this->setConnection($connection);
        
        if (! $this->hasSchemaTable()) {
            $this->createSchemaTable();
        }
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }
    
    /**
     * {@inheritdoc}
     */
    public function commitTransaction()
    {
        $this->connection->commit();
    }
    
    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction()
    {
        $this->connection->rollback();
    }


    /**
     * {@inheritdoc}
     */
    public function getColumnSqlDefinition(PhinxColumn $column)
    {
        $def = parent::getColumnSqlDefinition($column);
        if (! $column instanceof Column) return $def;

        $defs = explode(' ', $def);

        $define = [$defs[0]];

        /*
        if ($collation = $column->getCollation()) {
            $charset = explode('_', $collation);

            $define[] = sprintf('CHARACTER SET %s', $charset[0]);
            $define[] =  sprintf('COLLATE %s', $collation);
        } elseif ($charset = $column->getCharset()) {
            $define[] = sprintf('CHARACTER SET %s', $charset);
        }
         */

        $define = array_merge($define, array_slice($defs, 1));
        $define = join(' ', $define);

        return $define;
    }
    
    
    /**
     * get tables
     *
     * @return  array
     */
    public function getTables()
    {
        $options = $this->getOptions();
        $tables = [];

        foreach ($this->fetchAll(sprintf("select name from sqlite_master where type = 'table'")) as $row) {
            if ($row[0] == 'sqlite_sequence') continue;

            $table = new Table($row[0], [], $this);
            $tables[] = $table;
            $options = [];

            $describes = $this->fetchAll(sprintf('PRAGMA table_info(%s)', $this->quoteTableName($table->getName())));
            $primary_keys = [];
            foreach ($describes as $describe) {
                if ($describe['pk'] === '1') {
                    $primary_keys[] = $describe['name'];
                }
            }

            if (! $primary_keys || (count($primary_keys) === 1 && $primary_keys[0] === 'id')) {
            }

            // $this->table('foo', ['id' => 'foo_id']);
            elseif (count($primary_keys) === 1 && $primary_keys[0] !== 'id') {
                $options = ['id' => $primary_keys[0]];
            } else {
                $options = ['id' => false, 'primary_key' => $primary_keys];
            }

            $table->setOptions($options);
        }

        return $tables;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getColumns($tableName)
    {
        $columns = [];
        $rows = $this->fetchAll(sprintf('PRAGMA table_info(%s)', $this->quoteTableName($tableName)));
        foreach ($rows as $columnInfo) {
            if (! $columnInfo['type']) continue;

            $phinxType = $this->getPhinxType($columnInfo['type']);
            //$collation = $this->parseCollation($columnInfo['Collation']);

            $column = new Column();
            $column->setName($columnInfo['name'])
                   ->setNull($columnInfo['notnull'] != '0')
                   ->setDefault($columnInfo['dflt_value'])
                   ->setType($phinxType['name'])
                   ->setLimit($phinxType['limit']);

            if ($columnInfo['pk'] == '1') {
                $column->setIdentity(true);
            }

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhinxType($sqlTypeDef)
    {
        return parent::getPhinxType(strtolower($sqlTypeDef));
    }


    /**
     * {@inheritdoc}
     */
    public function getIndexes($tableName)
    {
        $indexes = [];
        $rows = $this->fetchAll(sprintf('pragma index_list(%s)', $tableName));

        foreach ($rows as $row) {
            $indexData = $this->fetchAll(sprintf('pragma index_info(%s)', $row['name']));
            if (!isset($indexes[$row['name']])) {
                $indexes[$row['name']] = array('index' => $row['name'], 'columns' => array());
            }
            foreach ($indexData as $indexItem) {
                $indexes[$row['name']]['columns'][] = strtolower($indexItem['name']);
            }
        }

        return $indexes;
    }
}

