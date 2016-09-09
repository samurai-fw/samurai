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

namespace Samurai\Samurai\Component\Routing\Rule;

use Closure;
use Samurai\Samurai\Component\Routing\Router;

/**
 * routing group rule
 *
 * @package     Samurai
 * @subpackage  Component.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class GroupRule implements RuleInterface
{
    /**
     * base rule
     *
     * @var     Rule
     */
    protected $baseRule;

    /**
     * child rules
     *
     * @var     array
     */
    protected $childRules = [];


    /**
     * constructor
     *
     * @param   Router  $router
     * @param   array   $option
     * @param   Closure $childClosure
     */
    public function __construct(Router $router, array $option, Closure $childClosure)
    {
        $this->baseRule = new HttpMethodRule();
        $this->router = new Router($router);

        $childClosure($this->router);
    }


    /**
     * prefix
     *
     * @param   string  $prefix
     * @return  Rule
     */
    public function prefix($prefix)
    {
        $this->baseRule->prefix($prefix);
    }


    /**
     * add get routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function get($path, $action = null)
    {
        return $this->router->get($path, $action);
    }
    
    /**
     * add post routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function post($path, $action = null)
    {
        return $this->router->post($path, $action);
    }
    
    /**
     * add put routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function put($path, $action = null)
    {
        return $this->router->put($path, $action);
    }
    
    /**
     * add patch routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function patch($path, $action = null)
    {
        return $this->router->patch($path, $action);
    }
    
    /**
     * add delete routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function delete($path, $action = null)
    {
        return $this->router->delete($path, $action);
    }

    /**
     * add any method routing
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function any($path, $action = null)
    {
        return $this->router->any($path, $action);
    }

    /**
     * add match routing
     *
     * @param   string  $path
     * @param   string|array    $method
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function match($path, $method, $action = null)
    {
        return $this->router->match($path, $method, $action);
    }

    /**
     * add controller routing
     * (http method not important)
     *
     * - /foo/index: FooController::indexAction
     * - /foo/show: FooController::showAction
     *
     * @param   string  $path
     * @param   mixed   $class
     * @param   array   $names
     */
    public function controller($path, $class, array $names = [])
    {
        return $this->router->controller($path, $class, $names);
    }

    /**
     * add group matching
     *
     * @param   array   $option
     * @return  GroupRule
     */
    public function group(array $option, Closure $closure)
    {
        return $this->router->group($option, $closure);
    }


    /**
     * {@inheritdoc}
     */
    public function match($path, $method = null)
    {
    }
}

