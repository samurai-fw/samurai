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

use Phinx\Config\Config as PhinxConfig;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Onikiri\Database;
use Samurai\Onikiri\Driver\Driver;

/**
 * Phinx config class wrapper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Config extends PhinxConfig
{
    /**
     * {@inheritdoc}
     */
    protected $values = [];


    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * construct
     */
    public function __construct()
    {
    }


    /**
     * initialize
     *
     * @param   string                      $alias
     * @param   Samurai\Onikiri\Database    $database
     */
    public function initialize($alias, Database $database)
    {
        $this['paths'] = [
            'migrations' => $this->loader->findFirst($this->application->config('directory.database.migration')) . DS . $alias
        ];
        $this['environments'] = [
            'default_migration_table' => 'samurai_migration_log',
            'default_database' => 'development',
        ];

        $environment = $this->application->getEnv();
        $this['environments'] = [
            $environment => [
                'adapter' => $this->toPhinxAdapterName($database->getDriver()),
                'host' => $database->getHostName(),
                'user' => $database->getUser(),
                'pass' => $database->getPassword(),
                'port' => $database->getPort(),
                'charset' => $database->getCharset(),
                'name' => $database->getDatabaseName(),
                'memory' => $this->toPhinxSqliteMemory($database->getDriver(), $database->getDatabaseName()),
            ]
        ];
    }


    /**
     * convert to phinx database adapter name.
     *
     * @param   Samurai\Onikiri\Driver\Driver   $driver
     */
    public function toPhinxAdapterName(Driver $driver)
    {
        if ($driver instanceof \Samurai\Onikiri\Driver\MySqlDriver) {
            return 'mysql';
        } elseif ($driver instanceof \Samurai\Onikiri\Driver\PgsqlDriver) {
            return 'pgsql';
        } elseif ($driver instanceof \Samurai\Onikiri\Driver\SqliteDriver) {
            return 'sqlite';
        }
    }


    /**
     * convert to phinx sqlite memory flag.
     *
     * @param   Samurai\Onikiri\Driver\Driver   $driver
     * @param   string  $database
     */
    public function toPhinxSqliteMemory(Driver $driver, $database)
    {
        return $driver instanceof \Samurai\Onikiri\Driver\SqliteDriver && $database === ':memory:';
    }
}

