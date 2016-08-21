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

namespace Samurai\Samurai\Component\Migration;

use Samurai\Raikiri\DependencyInjectable;

/**
 * migration system abstract helper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Helper
{
    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * get seeders
     *
     * @param   string  $database
     * @param   string  $name
     * @return  array
     */
    public function getSeeders($database, $name)
    {
        $seeders = [];
        $basename = $name ? $this->seederFileBaseNameStrategy($name) : '*';
        foreach ($this->loader->find($this->application->config('directory.database.seed') . DS . $database) as $dir) {
            foreach ($this->finder->path($dir)->name($basename)->fileOnly()->find() as $file) {
                $class = $file->getClassName();
                $seeder = new $class();
                $seeder->setContainer($this->raikiri());
                $seeders[] = $seeder;
            }
        }
        return $seeders;
    }
    
    /**
     * seeder name adjusting
     *
     * @param   string  $name
     * @return  string
     */
    public function seederNameStrategy($name)
    {
        return $name;
    }
    
    /**
     * seeder file base name adjusting
     *
     * @param   string  $name
     * @return  string
     */
    public function seederFileBaseNameStrategy($name)
    {
        return ucfirst($name) . 'Seeder.php';
    }
    
    /**
     * seeder file name adjusting
     *
     * @param   string  $database
     * @param   string  $name
     * @return  string
     */
    public function seederFileNameStrategy($database, $name)
    {
        return $database . DS . $this->seederFileBaseNameStrategy($name);
    }
    
    
    /**
     * get schema file
     *
     * @param   string  $database
     * @return  string
     */
    public function getSchemaFile($database)
    {
        $dir = $this->loader->find($this->application->config('directory.database.schema'))->first();
        return $dir . DS . $this->schemaFileNameStrategy($database);
    }
    
    
    /**
     * get schema yaml file
     *
     * @param   string  $database
     * @return  string
     */
    public function getSchemaYAMLFile($database)
    {
        $dir = $this->loader->find($this->application->config('directory.database.schema'))->first();
        return $dir . DS . $this->schemaYAMLFileNameStrategy($database);
    }


    /**
     * schema file name strategy
     *
     * @param   string  $database
     * @return  string
     */
    public function schemaFileNameStrategy($database)
    {
        return $this->schemaClassNameStrategy($database) . '.php';
    }

    /**
     * schema class name strategy
     *
     * @param   string  $database
     * @return  string
     */
    public function schemaClassNameStrategy($database)
    {
        return ucfirst($database) . 'Schema';
    }
    
    /**
     * schema yaml file name strategy
     *
     * @param   string  $database
     * @return  string
     */
    public function schemaYAMLFileNameStrategy($database)
    {
        return $database . '.yml';
    }
}

