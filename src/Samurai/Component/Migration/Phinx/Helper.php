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

use Samurai\Samurai\Component\Migration\Helper as AbstractHelper;
use Samurai\Samurai\Component\FileSystem\Directory;

/**
 * migration system phinx helper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Helper extends AbstractHelper
{

    /**
     * migration name adjusting
     *
     * "-" (underbar), "-" (hyphen) replace to CamelCase
     *
     * @param   string  $name
     * @return  string
     */
    public function nameStrategy($name)
    {
        $name = join('', array_map('ucfirst', explode('_', str_replace('-', '_', $name))));
        return $name;
    }


    /**
     * migration file name adjusting
     *
     * add version prefix (YmdHis + index)
     *
     * @param   string  $database
     * @param   string  $name
     * @return  string
     */
    public function fileNameStrategy($database, $name)
    {
        $name = join('_', array_map('lcfirst', preg_split('/(?=[A-Z])/', $name)));
        return $database . DS . $this->generateVersion() . $name . '.php';
    }


    /**
     * migration class name adjusting
     *
     * @param   string  $database
     * @param   string  $name
     * @return  string
     */
    public function classNameStrategy($database, $name)
    {
        return $name;
    }


    /**
     * migration namespace adjusting
     *
     * @param   Samurai\Samurai\Component\FileSystem\Directory  $dir
     * @param   string                                          $database
     * @param   string                                          $name
     * @return  string
     */
    public function namespaceStrategy(Directory $dir, $database, $name)
    {
        return '';
    }


    /**
     * generate version
     *
     * @return  string
     */
    public function generateVersion()
    {
        return sprintf('%s', date('YmdHis'));
    }
}
