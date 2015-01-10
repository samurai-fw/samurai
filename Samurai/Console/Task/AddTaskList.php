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

namespace Samurai\Console\Task;

use Samurai\Samurai\Component\Task\Task;
use Samurai\Samurai\Component\Task\Option;
use Samurai\Samurai\Component\Task\Processor;
use Samurai\Samurai\Component\Core\Skeleton;
use Samurai\Samurai\Exception\NotFoundException;
use Samurai\Samurai\Exception\NotImplementsException;

/**
 * Add task.
 *
 * this task add class, spec, view, and others.
 *
 * @package     Samurai.Console
 * @subpackage  Task.Add
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class AddTaskList extends Task
{
    /**
     * add a class.
     *
     * [usage]
     *   $ ./app add:class Foo\Bar\Zoo
     *
     * @option  extands,e               extends class.
     * @option  use-raikiri,r=true      use raikiri(di container).
     * @option  use-accessor,a          use accessor trait.
     */
    public function classTask(Option $option)
    {
        $current = $this->getRootAppDir($option);
        $base_dir = $current;

        foreach ($option->getArgs() as $arg) {
            $path = str_replace('\\', DS, $arg);
            $dir = dirname($path);
            if ($dir == '.') $dir = '';

            $skeleton = $this->getSkeleton('class');
            $class_name = basename($path, '.php');
            $namespace = str_replace(DS, '\\', $dir);

            $skeleton->assign('namespace', $namespace);
            $skeleton->assign('class', $class_name);
            $skeleton->assign('extends', $option->get('extends'));
            $skeleton->assign('use-raikiri', $option->get('use-raikiri'));
            $skeleton->assign('use-accessor', $option->get('use-accessor'));

            $file = $base_dir . DS . ($dir ? $dir . DS : '') . $class_name . '.php';
            $this->fileUtil->mkdirP(dirname($file));
            $this->fileUtil->putContents($file, $skeleton->render());

            $this->sendMessage('created class file. -> %s', $file);
        }
    }
    
    
    /**
     * add a component.
     *
     * [usage]
     *   $ ./app add:component Foo\Bar\Zoo
     *
     * @option  extends,e               extends class.
     * @option  use-raikiri,r=true      use raikiri(di container).
     * @option  use-accessor,a          use accessor trait.
     */
    public function componentTask(Option $option)
    {
        $root = $this->getRootAppDir($option);
        $current = $this->getCurrentAppDir($option);
        $prefix = trim(preg_replace('/^' . preg_quote($root, '/') . '/', '', $current), DS);

        $option->setArg(0, $prefix . DS . 'Component' . DS . $option->getArg(0));
        $this->task('add:class', $option);
    }


    /**
     * add a model.
     *
     * [usage]
     *   $ ./app add:model table_name
     */
    public function modelTask(Option $option)
    {
        $current = $this->getCurrentAppDir($option);
        $base_dir = $current;

        foreach ($option->getArgs() as $arg)
        {
            // table
            $table_class_name = $this->onikiri->config->getNamingStrategy()->aliasToTableClassName($arg);
            $filename = $table_class_name . '.php';
            $file = $this->loader->find($current . DS . $this->application->config('directory.model') . DS . $filename, true)->first();
            $namespace = $file->getNameSpace();

            $skeleton = $this->getSkeleton('modelTable');
            $skeleton->assign('alias', $arg);
            $skeleton->assign('namespace', $namespace);
            $skeleton->assign('class', $table_class_name);
            
            if (! $file->isExists() || $this->confirmation(['model table file(%s) is already exists. override ?', $file])) {
                $this->fileUtil->mkdirP(dirname($file));
                $this->fileUtil->putContents($file, $skeleton->render());
                $this->sendMessage('created model table file. -> %s', $file);
            }

            // entity
            $entity_class_name = $this->onikiri->config->getNamingStrategy()->aliasToEntityClassName($arg);
            $filename = $entity_class_name . '.php';
            $file = $this->loader->find($current . DS . $this->application->config('directory.model') . DS . $filename, true)->first();
            $namespace = $file->getNameSpace();

            $skeleton = $this->getSkeleton('modelEntity');
            $skeleton->assign('alias', $arg);
            $skeleton->assign('namespace', $namespace);
            $skeleton->assign('class', $entity_class_name);
            
            if (! $file->isExists() || $this->confirmation(['model entity file(%s) is already exists. override ?', $file])) {
                $this->fileUtil->mkdirP(dirname($file));
                $this->fileUtil->putContents($file, $skeleton->render());
                $this->sendMessage('created model entity file. -> %s', $file);
            }
        }
    }
    
    
    /**
     * add a controller.
     *
     * [usage]
     *   $ ./app add:action controller.action
     */
    public function actionTask(Option $option)
    {
        $current = $this->getCurrentAppDir($option);
        $base_dir = $current;

        foreach ($option->getArgs() as $arg)
        {
            // validation
            if (! preg_match('/^[\w_]+\.[\w_]+$/', $arg)) {
                $this->sendMessage('invalid format. -> %s', $arg);
                continue;
            }

            // controller
            list($controller, $action) = explode('.', $arg);
            $class_name = $this->actionChain->controllerClassNameStrategy($controller);
            $action_name = $this->actionChain->actionNameStrategy($action);
            $filename = str_replace('\\', DS, $class_name) . '.php';
            $file = $this->loader->find($current . DS . $this->application->config('directory.controller') . DS . $filename, true)->first();

            $skeleton = $this->getSkeleton('controller');
            $skeleton->assign('namespace', $file->getNameSpace());
            $skeleton->assign('class', $file->getClassName(false));
            
            if ($file->isExists()) {
                $this->sendMessage('controller file(%s) is already exists', $file);
            } else {
                $this->fileUtil->mkdirP(dirname($file));
                $this->fileUtil->putContents($file, $skeleton->render());
                $this->sendMessage('created controller file. -> %s', $file);
            }

            // action
            $ref = new \ReflectionClass($file->getClassName());
            if ($ref->hasMethod($action_name)) {
                $this->sendMessage('this action name is already defined. -> %s', $arg);
            } else {
                $contents = file_get_contents($file);
                $code = <<<EOL

    /**
     * [description]
     */
    public function $action_name()
    {
        // some implements

        return self::VIEW_TEMPLATE;
    }
EOL;
                $contents = preg_replace('/}[ \n]*$/', $code . "\n}\n", $contents);
                $this->fileUtil->putContents($file, $contents);
                
                $this->sendMessage('created action method. -> %s (%s)', $arg, $file);
            }

            // view
            $template = sprintf('%s/%s.%s', join(DS, array_map('ucfirst', explode('\\', $controller))), substr($action_name, 0, -6), $this->renderer->getSuffix());
            $file = $this->loader->find($current . DS . $this->application->config('directory.template') . DS . $template, true)->first();
            $skeleton = $this->getSkeleton('ViewContent', 'html');
            
            if ($file->isExists()) {
                $this->sendMessage('view file(%s) is already exists', $file);
            } else {
                $this->fileUtil->mkdirP(dirname($file));
                $this->console->log($skeleton->render());
                $this->fileUtil->putContents($file, $skeleton->render());
                $this->sendMessage('created view file. -> %s', $file);
            }
        }
    }


    /**
     * add a spec.
     *
     * [usage]
     *   $ ./app add:spec Foo/Bar/Zoo
     */
    public function specTask(Option $option)
    {
        $current = $this->getCurrentAppDir($option);
        $spec_dir = $this->loader->find($current . DS . $this->application->config('directory.spec'))->first();
        $spec_dir->absolutize();

        foreach ($option->getArgs() as $arg) {
            $path = $arg;
            $dir = dirname($path);
            if ($dir == '.') $dir = '';

            $skeleton = $this->getSkeleton('Spec');
            $base_dir = clone $spec_dir;
            $class_name = basename($path, '.php');
            $namespace = str_replace(DS, '\\', $dir);

            $skeleton->assign('namespace', $namespace ? $namespace . '\\' : '');
            $skeleton->assign('class', $class_name);
            $skeleton->assign('spec_namespace', $spec_dir->getNameSpace() . ($namespace ? '\\' . $namespace : ''));
            $skeleton->assign('spec_class', $class_name . 'Spec');

            $spec_file = $spec_dir->getRealPath() . DS . ($dir ? $dir . DS : '') . $class_name . 'Spec.php';
            $this->fileUtil->mkdirP(dirname($spec_file));
            $this->fileUtil->putContents($spec_file, $skeleton->render());

            $this->sendMessage('created spec file. -> %s', $spec_file);
        }
    }


    /**
     * add a task
     *
     * [usage]
     *   $ ./app add:task foo:bar:zoo
     */
    public function taskTask(Option $option)
    {
        $current = $this->getCurrentAppDir($option);
        $dir = $option->get('dir', $this->application->config('directory.task'));
        $task_dir = $this->loader->find($current . DS . $dir)->first();
        $task_dir->absolutize();

        foreach ($option->getArgs() as $arg) {

            try {
                $task = $this->taskProcessor->get($arg);

                // aldeady defined.
                $this->sendMessage('already defined. -> %s', $arg);
            }
            
            // TaskList not found.
            catch (NotFoundException $e) {
                $skeleton = $this->getSkeleton('TaskList');
                
                $names = explode(Processor::SEPARATOR, $arg);
                $method = array_pop($names) . 'Task';
                $class_name = $names ? ucfirst(array_pop($names)) . 'TaskList' : 'TaskList';

                $dir = $names ? join(DS, array_map('ucfirst', $names)) : '';
                $namespace = $task_dir->getNameSpace() . ($dir ? '\\' . str_replace(DS, '\\', $dir) : '');
                
                $skeleton->assign('namespace', $namespace);
                $skeleton->assign('class', $class_name);
                $skeleton->assign('method', $method);
                
                $task_file = $task_dir->getRealPath() . ($dir ? DS . $dir : '') . DS . $class_name . '.php';
                $this->fileUtil->mkdirP(dirname($task_file));
                $this->fileUtil->putContents($task_file, $skeleton->render());
                
                $this->sendMessage('created task file. -> %s (%s)', $arg, $task_file);
            }

            // Task method not found.
            catch (NotImplementsException $e) {
                $names = explode(Processor::SEPARATOR, $arg);
                $method = array_pop($names) . 'Task';
                $class_name = $names ? ucfirst(array_pop($names)) . 'TaskList' : 'TaskList';
                $dir = $names ? join(DS, array_map('ucfirst', $names)) : '';
                
                $task_file = $task_dir->getRealPath() . ($dir ? DS . $dir : '') . DS . $class_name . '.php';
                $contents = file_get_contents($task_file);
                $code = <<<EOL

    /**
     * [description]
     */
    public function $method(Option \$option)
    {
        // some implements
    }
EOL;
                $contents = preg_replace('/}[ \n]*$/', $code . "\n}\n", $contents);
                $this->fileUtil->putContents($task_file, $contents);
                
                $this->sendMessage('created task method. -> %s (%s)', $arg, $task_file);
            }
        }
    }
    
    
    /**
     * add a migration
     *
     * [usage]
     *   $ ./app add:migration title
     *
     * @option      database,d=base     set database name. (default is base)
     * @option      app-dir             set app dir.
     */
    public function migrationTask(Option $option)
    {
        $database = $option->get('database');
        $current = $this->getCurrentAppDir($option);
        $migration_dir = $this->loader->find($current . DS . $this->application->config('directory.database.migration'))->first();
        $migration_dir->absolutize();

        foreach ($option->getArgs() as $arg) {

            $name = $this->migrationHelper->nameStrategy($arg);
            $filename = $this->migrationHelper->fileNameStrategy($database, $name);
            $classname = $this->migrationHelper->classNameStrategy($database, $name);
            $namespace = $this->migrationHelper->namespaceStrategy($migration_dir, $database, $name);

            $skeleton = $this->getSkeleton('Migration');

            $base_dir = clone $migration_dir;
            
            $skeleton->assign('namespace', $namespace ? $namespace . '\\' : '');
            $skeleton->assign('class', $classname);
            
            $migration_file = $migration_dir->getRealPath() . DS . $filename;
            $this->fileUtil->mkdirP(dirname($migration_file));
            $this->fileUtil->putContents($migration_file, $skeleton->render());

            $this->sendMessage('created migration file. -> %s', $migration_file);
        }
    }




    /**
     * get skeleton.
     *
     * @param   string  $name
     * @return  Samurai\Samurai\Component\Core\Skeleton
     */
    private function getSkeleton($name, $suffix = 'php')
    {
        $file = $this->loader->find($this->application->config('directory.skeleton') . DS . ucfirst($name) . 'Skeleton.' . $suffix . '.twig')->first();
        $skeleton = new Skeleton($file);
        return $skeleton;
    }



    /**
     * get current dir in application.
     *
     * @return  string
     */
    public function getCurrentAppDir(Option $option)
    {
        // has targeted.
        if ($dir = $option->get('app-dir')) {
            return $dir[0] === '/' ? $dir : getcwd() . DS . $dir;
        }
        // or current dir.
        else {
            $current = getcwd();
            $default = null;
            foreach ($this->application->config('directory.apps') as $app) {
                if (strpos($current, $app['dir']) === 0) return $app['dir'];
                if (! $default) $default = $app['dir'];
            }
            return $default;
        }
    }
    
    /**
     * get root dir in application.
     *
     * @return  string
     */
    public function getRootAppDir(Option $option)
    {
        // has targeted.
        if ($dir = $option->get('root-dir')) {
            return $dir[0] === '/' ? $dir : getcwd() . DS . $dir;
        }
        // or current dir.
        else {
            $root = getcwd();
            $default = null;
            foreach ($this->application->config('directory.apps') as $app) {
                if (strpos($root, $app['dir']) === 0) return $app['root'];
                if (! $default) $default = $app['root'];
            }
            return $default;
        }
    }

}
