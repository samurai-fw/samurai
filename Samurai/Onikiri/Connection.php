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
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri;

use PDO;

/**
 * Connection (base is PDO)
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Connection extends PDO
{
    /**
     * count of number holder.
     *
     * @access  private
     * @var     int
     */
    private $count_numbered = 0;


    /**
     * @override
     */
    public function __construct($dsn, $user = null, $password = null, array $options = array())
    {
        parent::__construct($dsn, $user, $password, $options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('\\Samurai\\Onikiri\\Statement', array()));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    /**
     * For support number, named mixed placeholder.
     *
     * @override
     * @see     PDO::prepare
     */
    public function prepare($statement, $options = [])
    {
        // numbering placeholder to named placeholder.
        $statement = preg_replace_callback('/(^|\s|,)?\?(,|\s|$)?/', array($this, 'replaceNumberedHolder'), $statement);

        $sth = parent::prepare($statement, $options);
        $sth->setConnection($this);
        $this->count_numbered = 0;

        return $sth;
    }

    /**
     * replace numbered(?) holder.
     *
     * @access  private
     * @param   array   $matches
     * @return  string
     */
    private function replaceNumberedHolder(array $matches)
    {
        $holder = ':numbered_holder_' . $this->count_numbered;
        $this->count_numbered ++;
        return (isset($matches[1]) ? $matches[1] : '') . $holder . (isset($matches[2]) ? $matches[2] : '');
    }
}

