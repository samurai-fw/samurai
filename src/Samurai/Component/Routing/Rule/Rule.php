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
use Samurai\Samurai\Component\Routing\ActionCaller;


/**
 * Rule abstract class.
 *
 * @package     Samurai
 * @subpackage  Component.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Rule
{
    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * controller
     *
     * @var     string
     */
    public $controller;

    /**
     * action
     *
     * @var     mixed
     */
    public $action;

    /**
     * path
     *
     * @var     string
     */
    public $path;

    /**
     * params
     *
     * @var     array
     */
    public $params = [];

    /**
     * alias of setName
     *
     * @param   string  $name
     * @return  Rule
     */
    public function name($name)
    {
        return $this->setName($name);
    }

    /**
     * Set name.
     *
     * @param   string  $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * get name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set controller.
     *
     * @param   string  $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }


    /**
     * Get Controller
     *
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     *
     * format with controller: controller.action
     *
     * @param   string  $action
     */
    public function setAction($action)
    {
        if ($action instanceof Closure)
        {
            $this->action = $action;
            return;
        }
        else if (is_array($action) && is_callable($action))
        {
            $this->action = $action;
            return;
        }
        else if (strpos($action, '::') > 0)
        {
            $this->action = explode('::', $action);
            return;
        }

        $names = explode('.', $action);
        if (count($names) > 1) {
            $controller = array_shift($names);
            $action = array_shift($names);
            $this->setController($controller);
        } else {
            $action = array_shift($names);
        }
        $this->action = $action;
    }


    /**
     * Get action
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set path.
     *
     * @param   string  $path
     */
    public function setPath($path)
    {
        $parts = explode(DS, $path);
        $filename = array_pop($parts);

        // with format.
        if (preg_match('/^(.+?)\.(.+)$/', $filename, $matches)) {
            $filename = $matches[1];
            $format = $matches[2];
            $parts[] = $filename;
            $this->path = join(DS, $parts);
            $this->setParam('format', $format);

        // no format
        } else {
            $this->path = $path;
        }
    }

    /**
     * get path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Set params
     *
     * @param   string  $key
     * @param   string  $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * get param
     *
     * @param   string  $key
     * @return  mixed
     */
    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * get params
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }


    /**
     * matching to path.
     *
     * @param   string  $path
     * @param   string  $method
     * @return  boolean
     */
    abstract public function match($path, $method = null);
    
    
    /**
     * rule convert to action caller
     *
     * @param   string  $path
     * @param   string  $method
     * @return  ActionCaller
     */
    public function toActionCaller()
    {
        $actionCaller = new ActionCaller();

        $action = $this->getAction();
        if ($action instanceof Closure)
        {
            $actionCaller->byClosure($action);
        }
        else if (is_callable($action))
        {
            $actionCaller->byCallable($action);
        }
        else
        {
        }

        return $actionCaller;
    }


    /**
     * method name convert to url string
     *
     * @param   string  $method
     * @return  string
     */
    abstract public function methodName2URL($method);
}

