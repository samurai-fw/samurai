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

namespace Samurai\Samurai\Component\Core;

use Samurai\Samurai\Component\Core\Accessor;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Base skeleton.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Skeleton
{
    /**
     * @traits
     */
    use Accessor;

    /**
     * path of skeleton.
     *
     * @var     string
     */
    public $path;

    /**
     * variables.
     *
     * @var     array
     */
    public $vars = [];

    /**
     * twig
     *
     * @var     Twig_Environment
     */
    public $twig;


    /**
     * constructor
     *
     * @access  public
     * @param   string  $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }


    /**
     * assign variable.
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     */
    public function assign($key, $value)
    {
        $this->vars[$key] = $value;
    }


    /**
     * rendering skeleton
     *
     * @access  public
     * @return  string
     */
    public function render()
    {
        $this->initTwig();
        $contents = $this->twig->render(basename($this->path), $this->vars);
        return $contents;
    }


    /**
     * initialize twig.
     *
     * @access  public
     */
    public function initTwig()
    {
        if ($this->twig) return;

        // initialize twig
        Twig_Autoloader::register();
        $this->twig = new Twig_Environment();
        
        $loader = new Twig_Loader_Filesystem(dirname($this->path));
        $this->twig->setLoader($loader);
    }
}

