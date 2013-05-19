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

use Samurai\Samurai\Application;
use Samurai\Raikiri;
use Samurai\Samurai\Samurai;
use Samurai\Samurai\Config;

/**
 * Framework executer.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Framework extends Raikiri\Object
{
    /**
     * application instance
     *
     * @access  public
     * @var     Samurai\Samurai\Application
     */
    public $app;

    /**
     * @dependencies
     */
    public $Router;
    public $ActionChain;
    public $FilterChain;
    public $Response;

     
    /**
     * constructor
     *
     * @access  public
     * @param   Samurai\Samurai\Application
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->app = $app;
    }



    /**
     * execute.
     *
     * 1. init container.
     * 2. load config
     * 3. routing
     * 4. action chain
     * 5. filter chain
     *
     * @access  public
     */
    public function execute()
    {
        // init container.
        $this->_initContainer();

        // routing
        $this->_routing();

        // action chain.
        while ( $action = $this->ActionChain->getCurrentAction() ) {

            // clear.
            $this->FilterChain->clear();

            $this->FilterChain->setAction($action['controller'], $action['action']);
            $this->FilterChain->build();
            $this->FilterChain->execute();

            $this->ActionChain->next();
        }

        // response.
        $this->Response->execute();
    }


    /**
     * get application instance.
     *
     * @access  public
     * @return  Samurai\Samurai\Application
     */
    public function getApplication()
    {
        return $this->app;
    }



    /**
     * initialize container.
     *
     * @access  public
     */
    private function _initContainer()
    {
        $dicon = $this->app->config('dicon');
        $container = Raikiri\ContainerFactory::create();
        $container->import($dicon);

        $container->registerComponent('Framework', $this);
        $container->registerComponent('Application', $this->app);
        $container->registerComponent('Loader', $this->app->loader);
        $container->injectDependency($this);

        $this->app->setContainer($container);
    }


    /**
     * routing.
     *
     * @access  private
     */
    private function _routing()
    {
        // import.
        $file = $this->app->config('directory.app') . DS . $this->app->config('directory.config.routing') . '/routes.yml';
        $this->Router->import($file);

        // routing.
        $rule = $this->Router->routing();

        // add action chain.
        $this->ActionChain->addAction($rule->getController(), $rule->getAction());
    }
}

