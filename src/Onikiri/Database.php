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

use Samurai\Onikiri\Driver\SqliteDriver;
use Samurai\Samurai\Component\Core\Accessor;

/**
 * Database configuration and entity;
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Database
{
    /**
     * driver
     *
     * @var     Samurai\Onikiri\Driver\Driver
     */
    public $driver;

    /**
     * user
     *
     * @var     string
     */
    public $user;

    /**
     * password
     *
     * @var     string
     */
    public $password;

    /**
     * host name
     *
     * @var     string
     */
    public $host_name;

    /**
     * post
     *
     * @var     int
     */
    public $port;

    /**
     * charset
     *
     * @var     string
     */
    public $charset;

    /**
     * database name
     *
     * @var     string
     */
    public $database_name;

    /**
     * database dir (for sqlite)
     *
     * @var     string
     */
    public $database_dir;

    /**
     * use memory (only sqlite)
     *
     * @var     boolean
     */
    public $memory;

    /**
     * driver options
     *
     * @var     array
     */
    public $options = [];

    /**
     * connection
     *
     * @var     Samurai\Onikiri\Connection
     */
    public $connection;

    /**
     * slaves
     *
     * @var     array
     */
    private $_slaves = array();

    /**
     * master (if slave only.)
     *
     * @var     Samurai\Onikiri\Database
     */
    private $_master;

    /**
     * @trait
     */
    use Accessor;
    
    /**
     * target constants
     *
     * @const   string
     */
    const TARGET_AUTO = 'auto';
    const TARGET_MASTER = 'master';
    const TARGET_SLAVE = 'slave';


    /**
     * constructor.
     *
     * @param   array   $setting
     */
    public function __construct(array $setting = [])
    {
        foreach ($setting as $key => $value) {
            switch ($key) {
                case 'driver':
                case 'user':
                case 'port':
                case 'charset':
                case 'memory':
                    $this->{'set' . ucfirst($key)}($value);
                    break;
                case 'pass':
                    $this->setPassword($value);
                    break;
                case 'host':
                    $this->setHostName($value);
                    break;
                case 'database':
                    $this->setDatabaseName($value);
                    break;
                case 'slaves':
                    foreach ($value as $slave) {
                        $this->addSlave($slave);
                    }
                    break;
            }
        }
    }


    /**
     * Set driver
     *
     * @param   string  $name
     */
    public function setDriver($name)
    {
        $class = '\\Samurai\\Onikiri\\Driver\\' . ucfirst($name) . 'Driver';
        if (! class_exists($class)) throw new \InvalidArgumentException("No such driver. -> {$name}");
        
        $driver = new $class();
        $this->driver = $driver;
    }

    /**
     * Get driver.
     *
     * @return  Driver\Driver
     */
    public function getDriver()
    {
        if ($this->isSlave()) {
            return $this->_master->getDriver();
        }
        return $this->driver;
    }


    /**
     * Get user.
     *
     * @return  string
     */
    public function getUser()
    {
        if ($this->isSlave() && ! $this->user) {
            return $this->_master->getUser();
        }
        return $this->user;
    }


    /**
     * Get password
     *
     * @return  string
     */
    public function getPassword()
    {
        if ($this->isSlave() && ! $this->password) {
            return $this->_master->getPassword();
        }
        return $this->password;
    }


    /**
     * get host name
     *
     * @return  string
     */
    public function getHostName()
    {
        return $this->host_name;
    }


    /**
     * Get port number.
     *
     * @return  int
     */
    public function getPort()
    {
        if ($this->isSlave() && ! $this->port) {
            return $this->_master->getPort();
        }
        return $this->port;
    }


    /**
     * Get database name.
     *
     * @return  string
     */
    public function getDatabaseName()
    {
        if ($this->isSlave() && ! $this->database_name) {
            return $this->_master->getDatabaseName();
        }

        // sqlite data file
        if ($this->driver instanceof SqliteDriver) {
            if ($this->isUseMemory()) {
                return ':memory:';
            }
            if ($this->database_dir && $this->database_name[0] !== '/') {
                return $this->database_dir . '/' . $this->database_name;
            }
        }
        
        return $this->database_name;
    }


    /**
     * Get charset.
     *
     * @return  string
     */
    public function getCharset()
    {
        if ($this->isSlave() && ! $this->charset) {
            return $this->_master->getCharset();
        }

        return $this->charset;
    }


    /**
     * Add slave.
     *
     * @param   array   $setting
     */
    public function addSlave(array $setting)
    {
        $database = new Database($setting);
        $database->setMaster($this);
        $this->_slaves[] = $database;
    }

    /**
     * Clear slaves.
     */
    public function clearSlaves()
    {
        $this->_slaves = [];
    }

    /**
     * Get all slaves.
     *
     * @return  array
     */
    public function getSlaves()
    {
        return $this->_slaves;
    }
    
    /**
     * pick a slave.
     *
     * @return  Database
     */
    public function pickSlave()
    {
        if (! $this->hasSlave()) return $this;
        return $this->_slaves[array_rand($this->_slaves)];
    }


    /**
     * Set master configuration.
     *
     * @param   Samurai\Onikiri\Database    $master
     */
    public function setMaster(Database $master)
    {
        $this->_master = $master;
    }


    /**
     * Get options
     *
     * @return  array
     */
    public function getOptions()
    {
        $options = $this->options;
        return $options;
    }


    /**
     * connect to backend.
     *
     * @return  Connection
     */
    public function connect()
    {
        if ($this->connection) return $this->connection;

        $driver = $this->getDriver();
        return $this->connection = $driver->connect($this);
    }

    /**
     * disconnect from backend.
     */
    public function disconnect()
    {
        $this->connection = null;

        foreach ($this->getSlaves() as $slave) {
            $slave->disconnect();
        }
    }


    /**
     * has slaves ?
     *
     * @return  boolean
     */
    public function hasSlave()
    {
        return count($this->_slaves) > 0;
    }

    /**
     * is slave ?
     *
     * @return  boolean
     */
    public function isSlave()
    {
        return ! $this->isMaster();
    }

    /**
     * is master ?
     *
     * @return  boolean
     */
    public function isMaster()
    {
        return $this->_master === null;
    }


    /**
     * is use memory ?
     * (only sqlite)
     *
     * @return  boolean
     */
    public function isUseMemory()
    {
        if ($this->isSlave() && $this->memory === null) {
            return $this->_master->isUseMemory();
        }

        return $this->memory === true;
    }
}
