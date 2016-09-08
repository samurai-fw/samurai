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

namespace Samurai\Samurai\Component\Routing;

use Closure;
use Samurai\Samurai\Controller\Controller;
use Samurai\Raikiri\DependencyInjectable;

/**
 * Routing action caller.
 *
 * @package     Samurai
 * @subpackage  Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ActionCaller
{
    /**
     * action closure
     *
     * @var     Closure
     */
    protected $action;

    /**
     * controller instance
     *
     * @var     Object
     */
    protected $controller;
    
    /**
     * traits
     */
    use DependencyInjectable;


    /**
     * initialize by closure
     *
     * @param   Closure $closure
     */
    public function byClosure(Closure $closure)
    {
        $this->controller = new Controller();
        if ($this->hasContainer() && method_exists($this->controller, 'raikiri'))
            $this->controller->setContainer($this->raikiri());

        $this->action = $closure->bindTo($this->controller);
    }
    
    /**
     * initialize by callable
     *
     * @param   array   $callable   [$controller, $method]
     */
    public function byCallable(array $callable)
    {
        if (! is_callable($callable))
            throw new \InvalidArgumentException('not callable argument.');

        $this->byClassMethod($callable[0], $callable[1]);
    }

    /**
     * initialize by class and method
     *
     * @param   string|object   $class
     * @param   string  $method
     */
    public function byClassMethod($class, $method)
    {
        $this->controller = new $class();
        $this->controller = is_object($class) ? $class : new $class();
        if ($this->hasContainer() && method_exists($this->controller, 'raikiri'))
            $this->controller->setContainer($this->raikiri());

        $this->action = function() use($method)
        {
            $args = func_get_args();
            return call_user_func_array([$this->controller, $method], $args);
        };
    }


    /**
     * execute action
     *
     * @return  mixed
     */
    public function execute()
    {
        $action = $this->action;
        return $action();
    }
}

