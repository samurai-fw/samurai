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

namespace Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Core\Accessor;
use Samurai\Samurai\Component\Task\Task;
use Samurai\Samurai\Exception\NotImplementsException;
use Samurai\Raikiri\DependencyInjectable;

/**
 * task list class.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class TaskList
{
    /**
     * @traits
     */
    use Accessor;
    use DependencyInjectable;

    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * args
     *
     * @var     array
     */
    public $args = [];

    /**
     * options
     *
     * @var     array
     */
    public $options = [];

    /**
     * do method.
     *
     * @var     string
     */
    public $do = null;

    /**
     * output bredge component.
     *
     * @access  public
     */
    public $output;
    
    
    /**
     * execute this task pre setted.
     *
     * @param   array|Samurai\Samurai\Component\Task\Option $option
     */
    public function execute($options = [])
    {
        if (! is_array($options) && ! $options instanceof Option) throw new \InvalidArgumentException('invalid option');
        if (! $this->do) throw new \Samurai\Samurai\Exception\LogicException('preset task something do.');

        $option = $this->getOption($this->do);
        if (is_array($options)) {
            $option->importFromArray($options);
        } else {
            $option->import($options);
        }
        $option->validate();
        $this->{$this->do . 'Task'}($option);
    }


    /**
     * call other task
     *
     * @param   string  $name
     * @param   array|Samurai\Samurai\Component\Task\Option $option
     */
    public function task($name, $option = [])
    {
        $this->taskProcessor->execute($name, $option);
    }


    /**
     * send message to client.
     *
     * @param   string  $message
     */
    public function sendMessage()
    {
        if (! $this->output) return;

        $args = func_get_args();
        call_user_func_array([$this->output, 'send'], $args);
    }
    
    /**
     * ask prompt
     *
     * @param   string|array    $message
     * @param   boolean         $secret
     * @param   mixed           $default
     * @return  string
     */
    public function ask($message, $secret = false, $default = '')
    {
        return $this->response->ask($message, $secret, $default);
    }

    /**
     * confirmation prompt
     *
     * @param   string|array    $message
     * @param   array           $choices
     * @param   mixed           $default
     * @return  mixed
     */
    public function confirmation($message, $choices = ['y' => true, 'n' => false], $default = false)
    {
        return $this->response->confirmation($message, $choices, $default);
    }


    /**
     * get reflection instance.
     *
     * @return  Reflection
     */
    public function getReflection()
    {
        $reflection = new \ReflectionClass(get_class($this));
        return $reflection;
    }


    /**
     * get tasks
     *
     * @return  array
     */
    public function getTasks()
    {
        $tasks = [];

        foreach (get_class_methods($this) as $method) {
            if (preg_match('/Task$/', $method)) {
                $task = new Task($this, $this->getReflection()->getMethod($method));
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * get task
     *
     * @return  Task
     */
    public function get($name)
    {
        if (! $this->has($name)) throw new \InvalidArgumentException('no such task');

        $task = new Task($this, $this->getReflection()->getMethod($name . 'Task'));
        return $task;
    }


    /**
     * has task method ?
     *
     * @param   string  $name
     * @return  boolean
     */
    public function has($name)
    {
        return method_exists($this, $name . 'Task');
    }
}

