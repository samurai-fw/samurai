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

use Samurai\Samurai\Component\Core\YAML;

/**
 * Onikiri Manager.
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Manager
{
    /**
     * instance.
     *
     * @access  private
     * @var     Manager
     */
    private static $_instance;

    /**
     * database configurations
     *
     * @access  private
     * @var     array
     */
    private $_databases = array();

    /**
     * transactions.
     *
     * @access  private
     * @var     array
     */
    private $_transactions = array();


    /**
     * constructor
     *
     * @access  private
     */
    private function __construct()
    {
    }


    /**
     * Get instance
     *
     * @access  public
     * @return  Samurai\Onikiri\Manager
     */
    public static function singleton()
    {
        if ( ! self::$_instance ) {
            self::$_instance = new Manager();
        }
        return self::$_instance;
    }




    /**
     * import database configurations
     *
     * @access  public
     * @param   string  $file
     */
    public function importDatabase($file)
    {
        if ( ! file_exists($file) ) return;

        $settings = YAML::load($file);
        foreach ( $settings as $alias => $setting ) {
            $this->_databases[$alias] = new Database($setting);
        }
    }


    /**
     * get database configuration.
     *
     * @access  public
     * @param   string  $alias
     * @param   string  $target
     * @return  Database
     */
    public function getDatabase($alias, $target = Model::TARGET_MASTER)
    {
        $database = isset($this->_databases[$alias]) ? $this->_databases[$alias] : null;
        if ( $target === Model::TARGET_SLAVE ) {
            $database = $database->pickSlave();
        }
        return $database;
    }


    /**
     * get driver.
     *
     * @access  public
     * @param   string  $name
     * @return  Driver\Driver
     */
    public function getDriver($name)
    {
        $class = '\\Samurai\\Onikiri\\Driver\\' . ucfirst($name) . 'Driver';
        $driver = new $class();
        return $driver;
    }



    /**
     * connect to backend.
     *
     * @access  public
     * @param   string  $alias
     * @param   string  $target
     */
    public function establishConnection($alias, $target = Model::TARGET_MASTER)
    {
        $database = $this->getDatabase($alias, $target);
        return $database->connect();
    }




    /**
     * is in transaction ?
     *
     * @access  public
     * @return  boolean
     */
    public function inTx()
    {
        return count($this->_transactions) > 0;
    }
}
