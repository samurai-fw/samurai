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

namespace Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\Migration\Phinx\Adapter\MysqlAdapter;
use Samurai\Samurai\Component\Migration\Phinx\Adapter\SQLiteAdapter;
use Samurai\Raikiri\DependencyInjectable;
use Phinx\Migration\Manager as PhinxManager;
use Phinx\Migration\Manager\Environment;
use Phinx\Db\Adapter\AdapterFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Phinx manager wrapper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Manager extends PhinxManager
{
    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * construct
     */
    public function __construct(Config $config, OutputInterface $output)
    {
        parent::__construct($config, $output);

        AdapterFactory::instance()->registerAdapter('mysql', 'Samurai\Samurai\Component\Migration\Phinx\Adapter\MysqlAdapter');
        AdapterFactory::instance()->registerAdapter('sqlite', 'Samurai\Samurai\Component\Migration\Phinx\Adapter\SQLiteAdapter');
    }


    /**
     * {@inheritdoc}
    public function getEnvironment($name)
    {
        if (isset($this->environments[$name])) return $this->environments[$name];

        $ref = $this;

        $environment = parent::getEnvironment($name);
        $environment->registerAdapter('mysql', function(Environment $env){
            return new MysqlAdapter($env->getOptions(), $env->getOutput());
        });
        $environment->registerAdapter('sqlite', function(Environment $env) use ($ref) {
            $adapter = new SQLiteAdapter($env->getOptions(), $env->getOutput());
            $adapter->setContainer($ref->raikiri());
            return $adapter;
        });

        return $environment;
    }
     */
}

