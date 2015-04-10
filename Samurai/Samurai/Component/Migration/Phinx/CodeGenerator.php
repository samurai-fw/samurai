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

namespace Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Phinx\Db\Table\Column;

/**
 * Phinx code generator.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class CodeGenerator
{
    /**
     * generate table options
     *
     * @param   Table   $table
     * @return  string
     */
    public function generateTableOptions(Table $table)
    {
        $code = [];
        $options = $table->getOptions();
        
        $code = $this->valuetize($options);
        return $code === '[]' ? '' : ', ' . $code;
    }


    /**
     * generate column options
     *
     * @param   Column  $column
     * @return  string
     */
    public function generateColumnOptions(Column $column)
    {
        $options = [];

        $keys = ['length', 'default', 'null', 'precision', 'scale', 'after', 'update', 'comment'];
        foreach ($keys as $key) {
            $method = "get{$key}";
            if ($key === 'length') $method = 'getLimit';

            $value = $column->$method();
            if ($value === null) continue;
            if ($key === 'null' && $value === false) continue;

            $options[$key] = $value;
        }
        
        $code = $this->valuetize($options);
        return $code === '[]' ? '' : ', ' . $code;
    }


    /**
     * valuetize
     *
     * @param   mixed   $value
     * @return  string
     */
    public function valuetize($value)
    {
        switch (true) {
            case $value === true:
                return 'true';
            case $value === false:
                return 'false';
            case is_array($value):
                $_ = [];
                foreach ($value as $k => $v) {
                    if (is_int($k)) {
                        $_[] = $this->valuetize($v);
                    } else {
                        $_[] = "'{$k}' => " . $this->valuetize($v);
                    }
                }
                return '[' . join(', ', $_) . ']';
            case is_string($value):
                $value = "'{$value}'";
            default:
                return (string)$value;
        }
    }


    /**
     * is single primary key column ?
     *
     * @param   Table   $table
     * @param   Column  $column
     * @return  boolean
     */
    public function isSinglePrimaryKeyColumn(Table $table, Column $column)
    {
        $options = $table->getOptions();

        if (! $options && $column->getName() === 'id') return true;
        if (isset($options['id']) && $options['id'] == $column->getName()) return true;

        return false;
    }
}

