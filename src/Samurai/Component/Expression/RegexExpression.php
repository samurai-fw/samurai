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

namespace Samurai\Samurai\Component\Expression;

/**
 * Regexp expression class.
 *
 * @package     Samurai
 * @subpackage  Component.Expression
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class RegexExpression extends Expression
{
    /**
     * delimiter string
     *
     * @access  public
     * @var     string
     */
    public $delimiter = '/';

    /**
     * regex options
     *
     * @access  public
     * @var     string
     */
    public $options;


    /**
     * constructor
     *
     * @access  public
     * @param   string  $value
     */
    public function __construct($value)
    {
        if (strlen($value) > 3 && preg_match('/(...+?)([imsxeADSUXJu]+)?$/', $value, $matches)) {
            $s = substr($matches[1], 0, 1);
            $e = substr($matches[1], -1);
            if ($s !== $e || preg_match('/[a-z0-9\\s\\\\]/i', $s)) {
                throw new Exception\InvalidExpressionException('invalid regex format.');
            }
            $this->value = substr($matches[1], 1, -1);
            $this->delimiter = $s;
            $this->options = isset($matches[2]) ? $matches[2] : '';
        } else {
            throw new Exception\InvalidExpressionException('invalid regex format.');
        }
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function isMatch($value)
    {
        return (boolean)preg_match($this->getRegexPattern(), $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getRegexPattern()
    {
        return "{$this->delimiter}{$this->value}{$this->delimiter}{$this->options}";
    }
}

