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

use Samurai\Samurai\Component\Routing\Exception\NotFoundException;
use Samurai\Raikiri\DependencyInjectable;

/**
 * Routing class.
 *
 * URL dispatch to action.
 *
 * @package     Samurai
 * @subpackage  Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Router
{
    /**
     * root routing.
     *
     * @access  protected
     * @var     Rule\RootRule
     */
    protected $_root;

    /**
     * default routing.
     *
     * @access  protected
     * @var     Rule\DefaultRule
     */
    protected $_default;

    /**
     * rules
     *
     * @var     array
     */
    protected $rules = [];
    protected $_rules = array();

    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * constructor.
     *
     * @access  public
     */
    public function __construct()
    {
        $this->_default = new Rule\DefaultRule();
    }



    /**
     * dispatch to action
     *
     * @param   string  $path
     * @param   string  $method
     * @return  Closure
     */
    public function dispatch($path = '/', $method = 'GET')
    {
        foreach ($this->rules as $rule)
        {
            if ($rule->match($path, $method))
                return $rule->toActionCaller($path, $method);
        }
        throw new NotFoundException($path);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_GET, $action);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_POST, $action);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_PUT, $action);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_PATCH, $action);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_DELETE, $action);
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
        return $this->match($path, Rule\HttpMethodRule::HTTP_METHOD_ANY, $action);
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
        $rule = new Rule\HttpMethodRule();
        $rule->setPath($path);
        $rule->setMethod($method);
        $rule->setAction($action);
        $this->rules[] = $rule;
        return $rule;
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
        $methods = get_class_methods($class);

        foreach ($methods as $method)
        {
            // xxxxAction
            if (! preg_match('/(.+)Action$/i', $method, $matches))
                continue;

            if (is_object($class))
                $rule = $this->any($path, [$class, $method]);
            else
                $rule = $this->any($path, $class . '::' . $method);

            $rule->restful(false);
            $rule->setPath($path . '/' . $rule->methodName2URL($method));
        }
    }




    /**
     * import routing config.
     *
     * @access  public
     * @param   string  $file
     */
    public function import($file)
    {
        $rules = $this->yaml->load($file);

        foreach ($rules as $rule) {
            list($key, $value) = each($rule);
            switch ($key) {
                case 'root':
                    $this->setRoot($value);
                    break;
                case 'match':
                    $this->addMatchRule($value);
                    break;
            }
        }
    }


    /**
     * set root routing
     *
     * @access  public
     * @param   string
     */
    public function setRoot($value)
    {
        $rule = new Rule\RootRule($value);
        $this->_root = $rule;
    }


    /**
     * Add mathing rule.
     *
     * @access  public
     * @param   array   $value
     */
    public function addMatchRule(array $value)
    {
        $rule = new Rule\MatchRule($value);
        $this->_rules[] = $rule;
    }





    /**
     * routing.
     *
     * @access  public
     * @return  Rule\Rule
     */
    public function routing()
    {
        // has dispatch.
        $route = null;
        if ($action = $this->getDispatchAction()) {
            $route = new Rule\MatchRule(array('action' => $action));
        }

        // root rule.
        $path = $this->request->getPath();
        if (! $route && $this->_root && $this->_root->match($path)) {
            $route = $this->_root;
        }

        // match rule.
        if (! $route) {
            foreach ($this->_rules as $rule) {
                if ($rule->match($path)) {
                    $route = $rule;
                    break;
                }
            }
        }

        // default rule.
        if (! $route && $this->_default && $this->_default->match($path)) {
            $route = $this->_default;
        }

        // not found
        if (! $route || ! $this->isActionExists($route)) {
            $route = new Rule\NotFoundRule();
        }
        return $route;
    }


    /**
     * Get dispatched action
     *
     * enable target action name by request key.
     * ex. <input type="submit" name="dispatch-controller-action" value="submit" />
     *
     * @access  public
     * @return  string
     */
    public function getDispatchAction()
    {
        $params = $this->request->getAll();
        foreach (array_keys($params) as $key) {
            if (preg_match('/^dispatch-(.+)/', $key, $matches)) {
                $action = str_replace('-', '.', $matches[1]);
                return $action;
            }
        }
    }


    /**
     * is exists targeted action ?
     *
     * @param   Samurai\Samurai\Component\Routing\Rule\Rule
     * @return  boolean
     */
    public function isActionExists(Rule\Rule $rule)
    {
        return $this->actionChain->existsController($rule->getController(), $rule->getAction());
    }
}

