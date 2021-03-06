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

namespace Samurai\Samurai\Component\Migration\Phinx\Db;

use Phinx\Db\Table as PhinxTable;
use Phinx\Db\Table\Index;

/**
 * Phinx table wrapper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Table extends PhinxTable
{
    /**
     * comment
     *
     * @var     string
     */
    protected $comment;


    /**
     * addColumn wrapper.
     *
     * {@inheritdoc}
     */
    public function addColumn($columnName, $type = null, $options = [])
    {
        $column = new Column();
        $column->setName($columnName);
        $column->setType($type);
        $column->setOptions($options);

        $column->setTable($this);

        parent::addColumn($column);

        return $column;
    }
    
    /**
     * changeColumn wrapper.
     *
     * {@inheritdoc}
     */
    public function changeColumn($columnName, $newColumnType, $options = [])
    {
        if (!$newColumnType instanceof Column) {
            $newColumn = new Column();
            $newColumn->setType($newColumnType);
            $newColumn->setOptions($options);
        } else {
            $newColumn = $newColumnType;
        }

        return parent::changeColumn($columnName, $newColumn);
    }

    /**
     * add column bridge
     *
     * @param   string  $name
     * @param   string  $type
     * @param   array   $options
     * @return  Samurai\Samurai\Component\Migration\Phinx\Db\Column
     */
    public function column($name, $type = null, $options = [])
    {
        return $this->addColumn($name, $type, $options);
    }


    /**
     * set comment.
     *
     * but table comment is not supported in phinx.
     * this method is not working!
     *
     * @param   string  $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * get comment
     *
     * @return  string
     */
    public function getComment()
    {
        return $this->comment;
    }


    /**
     * set primary key
     *
     * @param   string  $column
     */
    public function setPrimaryKey()
    {
        $columns = func_get_args();
        $this->options['primary_key'] = $columns;
    }


    /**
     * {@inheritdoc}
     */
    public function addIndex($columns, $options = [])
    {
        if (! $columns instanceof Index) {
            $index = new Index();
            if (is_string($columns)) {
                $columns = [$columns];
            }
            $index->setColumns($columns);
            $index->setOptions($options);
            $index->setName(join('_', array_merge([$this->getName()], $columns)));
        } else {
            $index = $columns;
        }

        return parent::addIndex($index);
    }

    /**
     * get defined indexes
     *
     * @return  array
     */
    public function getDefinedIndexes()
    {
        return $this->getAdapter()->getIndexes($this->getName());
    }


    /**
     * add timestamps
     */
    public function addTimestamps($logical_delete = true)
    {
        $this->addColumn('created_at', 'integer', ['signed' => false, 'null' => true, 'comment' => 'created at timestamp']);
        $this->addColumn('updated_at', 'integer', ['signed' => false, 'null' => true, 'comment' => 'updated at timestamp']);

        if ($logical_delete) {
            $this->addColumn('deleted_at', 'integer', ['signed' => false, 'null' => true, 'comment' => 'deleted at timestamp']);
            $this->addColumn('active', 'boolean', ['default' => 1, 'comment' => 'logical delete flag']);
        }

        return $this;
    }
}

