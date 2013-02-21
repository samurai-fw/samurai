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
 * @subpackage  Driver
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;

/**
 * Driver for mysql class.
 *
 * @package     Onikiri
 * @subpackage  Driver
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class MysqlDriver extends Driver
{
    /**
     * @implements
     */
    public function connect(Database $database)
    {
        $dsn = $this->makeDsn($database);
        $con = new Connection($dsn, $database->getUser(), $database->getPassword(), $database->getOptions());
        return $con;
    }


    /**
     * @implements
     */
    public function makeDsn(Database $database)
    {
        $dsn = 'mysql:';
        $info = array();

        // database name
        $info[] = 'dbname=' . $database->getDatabaseName();

        // host name
        $info[] = 'host=' . $database->getHostName();

        // port
        if ( $port = $database->getPort() ) {
            $info[] = 'port=' . $port;
        }

        $dsn = $dsn . join(';', $info);
        return $dsn;
    }
}

