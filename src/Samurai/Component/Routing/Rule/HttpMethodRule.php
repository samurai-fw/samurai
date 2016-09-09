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

/**
 * http method routing rule
 *
 * @package     Samurai
 * @subpackage  Component.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HttpMethodRule extends MatchRule
{
    /**
     * method
     *
     * @var     array
     */
    protected $method = [self::HTTP_METHOD_GET];

    /**
     * restful
     *
     * @var     boolean
     */
    protected $restful = true;

    /**
     * secure only
     *
     * null: any
     * true: https only
     * false: http only
     */
    protected $secure;

    /**
     * http method const
     *
     * @const   string
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_PATCH = 'PATCH';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_ANY = 'GET|POST|PUT|PATCH|DELETE';


    /**
     * set method
     *
     * @param   string  $method
     * @return  Rule
     */
    public function setMethod($method)
    {
        if (is_string($method))
            $method = explode('|', $method);

        $this->method = array_map('strtoupper', $method);

        return $this;
    }

    /**
     * restful
     *
     * @param   boolean     $flag
     * @return  Rule
     */
    public function restful($flag = true)
    {
        $this->restful = $flag;
        return $this;
    }

    /**
     * secure only
     *
     * @return  Rule
     */
    public function secure()
    {
        $this->secure = true;
        return $this;
    }

    /**
     * not secure only
     */
    public function notSecure()
    {
        $this->secure = false;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function match($path, $method = self::HTTP_METHOD_GET)
    {
        $match = parent::match($path);
        return $match && in_array(strtoupper($method), $this->method) && $this->checkSecure();
    }


    /**
     * checking secure setting
     *
     * @return  boolean
     */
    public function checkSecure()
    {
        if ($this->secure === null)
            return true;

        $in_secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        return ($this->secure && $in_secure) || (! $this->secure && ! $in_secure);
    }


    /**
     * getFooBarZooAction to foo-bar-zoo
     * 
     * {@inheritdoc}
     */
    public function methodName2URL($method)
    {
        if (! $this->restful)
            return parent::methodName2URL($method);

        $method = preg_replace('/Action$/i', '', $method);
        $method = preg_replace('/^(' . self::HTTP_METHOD_ANY . ')/i', '', $method);
        return trim(strtolower(preg_replace('/[A-Z]/', '-\0', $method)), '-');
    }
}

