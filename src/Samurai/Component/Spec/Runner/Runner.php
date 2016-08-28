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

namespace Samurai\Samurai\Component\Spec\Runner;

use Samurai\Samurai\Component\FileSystem\File;
use Samurai\Samurai\Component\Core\Accessor;

/**
 * base runner
 *
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Runner
{
    /**
     * workspace
     *
     * @access  public
     * @var     string
     */
    public $workspace;

    /**
     * target path.
     *
     * @access  public
     * @var     string
     */
    public $targets = [];

    /**
     * loaded configuraton
     *
     * @var     array
     */
    protected $config;

    /**
     * @dependencies
     */
    public $finder;
    public $yaml;

    /**
     * @traits
     */
    use Accessor;


    /**
     * initialize
     */
    public function initialize()
    {
        $this->loadConfigurationFile();
    }


    /**
     * set workspace path.
     *
     * @param   string  $dir
     */
    public function setWorkspace($dir)
    {
        $this->workspace = $dir;
    }

    /**
     * get workspace path.
     *
     * @return  string
     */
    public function getWorkspace()
    {
        if ($this->workspace)
            return $this->workspace;

        return dirname($this->findConfigurationFile());
    }


    /**
     * set target path.
     *
     * @access  public
     * @param   string  $path
     */
    public function addTarget($path)
    {
        $this->targets[] = $path;
    }




    /**
     * run spec.
     */
    abstract public function run();


    /**
     * get configuration file.
     *
     * @return  string
     */
    abstract public function getConfigurationFileName();

    /**
     * find configuration file
     *
     * @param   string  $filename
     * @param   string  $pwd
     * @return  string
     */
    public function findConfigurationFile($filename = null, $pwd = null)
    {
        $filename = $filename ?: $this->getConfigurationFileName();
        $pwd = $pwd ?: getcwd();
        
        $dirs = explode(DS, $pwd);
        $find = false;
        do {
            $dir = join(DS, $dirs);
            $config_file_path = $dir . DS . $filename;
            if (file_exists($config_file_path) && is_file($config_file_path))
                return $config_file_path;
        } while (array_pop($dirs));

        return $pwd . DS . $filename;
    }

    /**
     * load configuraton file
     *
     * @param   string  $file
     */
    public function loadConfigurationFile($file = null)
    {
        $file = $file ?: $this->findConfigurationFile();
        if (! file_exists($file))
            return;

        $this->config = $this->yaml->load($file);
    }

    /**
     * get config
     *
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getConfig($key, $default = null)
    {
        return empty($this->config[$key]) ? $default : $this->config[$key];
    }

    /**
     * get spec file
     *
     * @param   string  $class
     * @return  File
     */
    abstract public function getSpecFile($class);

    /**
     * get spec dir
     *
     * @param   string  $class
     */
    public function getSpecDir($class)
    {
        return $this->getSpecFile($class)->getDirectory();
    }
}

