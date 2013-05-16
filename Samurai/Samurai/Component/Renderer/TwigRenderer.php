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

namespace Samurai\Samurai\Component\Renderer;

use Twig_Environment;

/**
 * Renderer Twig bridge.
 *
 * @package     Samurai
 * @subpackage  Component.Renderer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class TwigRenderer extends Renderer
{
    /**
     * template suffix.
     *
     * @access  public
     * @var     string
     */
    public $template_suffix = 'html.twig';

    /**
     * variables
     *
     * @access  private
     * @var     array
     */
    private $_variables = array();


    /**
     * @implements
     */
    public function initEngine()
    {
        $engine = new Twig_Environment();
        return $engine;
    }


    /**
     * @implements
     */
    public function getSuffix()
    {
        return $this->template_suffix;
    }


    /**
     * @implements
     */
    public function set($name, $value)
    {
        $this->_variables[$name] = $value;
    }


    /**
     * @implements
     */
    public function render($template)
    {
        $result = $this->_engine->render($template, $this->_variables);
        return $result;
    }
}

