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

use Phinx\Db\Table\Column;
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
    public function getColumnSqlDefinition(Column $column)
    {
        $def = parent::getColumnSqlDefinition($column);
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
}

