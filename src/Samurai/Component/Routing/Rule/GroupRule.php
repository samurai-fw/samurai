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
    protected $children = [];


    /**
     * constructor
     *
     * @param   array   $rule
     * @param   Closure $closure
     */
    public function __construct(array $rule, Closure $closure)
    {
        $this->baseRule = new HttpMethodRule($rule);
        $closure($this);
    }
    

    /**
     * set group
     *
     * @param   GroupRule   $group
     * @return  Rule
     */
    public function setGroup(GroupRule $group)
    {
        $this->baseRule->setGroup($group);
    }


    /**
     * get method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function get($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_GET, $action);
    }
    
    /**
     * post method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function post($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_POST, $action);
    }
    
    /**
     * put method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function put($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_PUT, $action);
    }
    
    /**
     * patch method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function patch($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_PATCH, $action);
    }
    
    /**
     * delete method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function delete($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_DELETE, $action);
    }
    
    /**
     * any method matching
     *
     * @param   string  $path
     * @param   string|Closure  $action
     * @return  Rule
     */
    public function any($path, $action = null)
    {
        return $this->match($path, HttpMethodRule::HTTP_METHOD_ANY, $action);
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
        $rule = new HttpMethodRule();
        $rule->setPath($path);
        $rule->setMethod($method);
        $rule->setAction($action);
        $rule->setGroup($this);
        $this->children[] = $rule;
        return $rule;
    }


    /**
     * group nesting
     *
     * @param   array   $rule
     * @param   Closure $closure
     */
    public function group(array $rule, Closure $closure)
    {
        $rule = new GroupRule($rule, $closure);
        $rule->setGroup($this);
        $this->children[] = $rule;
        return $rule;
    }
    
    
    /**
     * get path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->baseRule->getPath();
    }


    /**
     * {@inheritdoc}
     */
    public function matching($path, $method = HttpMethodRule::HTTP_METHOD_GET)
    {
        foreach ($this->children as $child)
        {
            if ($child->matching($path, $method))
                return true;
        }
        return false;
    }
    
    
    /**
     * checking domain
     *
     * @return  boolean
     */
    public function checkDomain()
    {
        return $this->baseRule->checkDomain();
    }
}

