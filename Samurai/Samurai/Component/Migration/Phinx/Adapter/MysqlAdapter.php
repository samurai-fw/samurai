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

use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Phinx\Db\Table\Column as PhinxColumn;
use Phinx\Db\Adapter\MysqlAdapter as PhinxMysqlAdapter;

/**
 * Phinx mysql adapter wrapper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class MysqlAdapter extends PhinxMysqlAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getColumnSqlDefinition(PhinxColumn $column)
    {
        $def = parent::getColumnSqlDefinition($column);
        if (! $column instanceof Column) return $def;

        $defs = explode(' ', $def);

        $define = [$defs[0]];

        if ($collation = $column->getCollation()) {
            $charset = explode('_', $collation);

            $define[] = sprintf('CHARACTER SET %s', $charset[0]);
            $define[] =  sprintf('COLLATE %s', $collation);
        } elseif ($charset = $column->getCharset()) {
            $define[] = sprintf('CHARACTER SET %s', $charset);
        }

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

        foreach ($this->fetchAll(sprintf('SHOW TABLES IN %s', $this->quoteTableName($options['name']))) as $row) {
            $table = new Table($row[0], [], $this);
            $tables[] = $table;
            $options = [];

            $describes = $this->fetchAll(sprintf('SHOW COLUMNS IN %s', $this->quoteTableName($table->getName())));
            $primary_keys = [];
            $auto_increment = false;
            foreach ($describes as $describe) {
                if ($describe['Key'] === 'PRI') {
                    $primary_keys[] = $describe['Field'];
                    if ($describe['Extra'] === 'auto_increment') {
                        $auto_increment = true;
                    }
                }
            }

            // $this->table('foo');
            if (! $primary_keys || (count($primary_keys) === 1 && $primary_keys[0] === 'id')) {
            }

            // $this->table('foo', ['id' => 'foo_id']);
            elseif (count($primary_keys) === 1 && $primary_keys[0] !== 'id' && $auto_increment) {
                $options = ['id' => $primary_keys[0]];
            }

            // $this->table('foo', ['id' => false, 'primary_key' => ['user_id']]);
            // $this->table('foo', ['id' => false, 'primary_key' => ['user_id', 'parent_id']]);
            else {
                $options = ['id' => false, 'primary_key' => $primary_keys];
            }

            $table->setOptions($options);
            //var_dump($table);
        }

        return $tables;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getColumns($tableName)
    {
        $columns = array();
        $rows = $this->fetchAll(sprintf('SHOW FULL COLUMNS FROM %s', $this->quoteTableName($tableName)));
        foreach ($rows as $columnInfo) {

            $phinxType = $this->getPhinxType($columnInfo['Type']);
            $collation = $this->parseCollation($columnInfo['Collation']);

            $column = new Column();
            $column->setName($columnInfo['Field'])
                   ->setNull($columnInfo['Null'] != 'NO')
                   ->setDefault($columnInfo['Default'])
                   ->setType($phinxType['name'])
                   ->setLimit($phinxType['limit'])
                   ->setCharset($collation['charset'])
                   ->setCollation($collation['collation'])
                   ->setComment($columnInfo['Comment']);

            if ($columnInfo['Extra'] == 'auto_increment') {
                $column->setIdentity(true);
            }

            $columns[] = $column;
        }

        return $columns;
    }


    /**
     * parse collation
     *
     * @param   string  $collation
     * @return  array   charaset and collation
     */
    public function parseCollation($collation)
    {
        $parsed = ['charset' => null, 'collation' => null];
        if ($collation === null) return $parsed;

        $charset = explode('_', $collation)[0];
        $parsed['charset'] = $charset;
        if ($charset === 'ascii') return $parsed;

        $parsed['collation'] = $collation;
        return $parsed;
    }
}

