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

namespace Samurai\Samurai\Component\Spec\Runner;

use Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator;
use Samurai\Samurai\Component\Spec\PHPSpec\Input;
use Samurai\Samurai\Component\Spec\PHPSpec\ApplicationMaintainer;
use Samurai\Samurai\Component\Spec\PHPSpec\DIContainerMaintainer;
use Samurai\Samurai\Component\Spec\PHPSpec\PSR0Locator;
use Samurai\Samurai\Component\FileSystem\File;
use Samurai\Samurai\Component\FileSystem\Directory;
use Samurai\Samurai\Exception\NotFoundException;
use PhpSpec\Console\Application;

/**
 * spec runner for PHPSpec.
 *
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PHPSpecRunner extends Runner
{
    /**
     * @dependencies
     */
    public $request;
    public $application;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // cd
        $pwd = getcwd();
        chdir($this->getWorkspace());

        $args = [];
        $args[] = $this->request->getScriptName();
        $args[] = 'run';
        $args[] = '--verbose';
        foreach ($this->targets as $target) {
            $args[] = $target;
        }
        $input = new Input($args);
        $app = new Application(\Samurai\Samurai\Samurai::getVersion());

        // override
        $container = $app->getContainer();
        $container->set('samurai.container', $this->application->getContainer());

        $container->set('runner.specification', function($c) {
            return new PHPSpecSpecificationRunner(
                $c->get('event_dispatcher'),
                $c->get('runner.example')
            );
        });

        $container->set('runner.maintainers.application', function($c) {
            $maintainer = new ApplicationMaintainer(
                $c->get('formatter.presenter'),
                $c->get('unwrapper')
            );
            $maintainer->Container = $c->get('samurai.container');
            return $maintainer;
        });
        $container->set('runner.maintainers.dicontainer', function($c) {
            $maintainer = new DIContainerMaintainer(
                $c->get('formatter.presenter'),
                $c->get('unwrapper')
            );
            $maintainer->Container = $c->get('samurai.container');
            return $maintainer;
        });

        $container->addConfigurator(function($c) {
            $suites = $c->getParam('suites', array('main' => ''));

            foreach ($suites as $name => $suite) {
                $suite = is_array($suite) ? $suite : array('namespace' => $suite);
                $srcNS = isset($suite['namespace']) ? $suite['namespace'] : '';
                $specNS = isset($suite['spec_namespace']) ? $suite['spec_namespace'] : 'spec';
                $srcPath = isset($suite['src_path']) ? $suite['src_path'] : 'src';
                $specPath = isset($suite['spec_path']) ? $suite['spec_path'] : 'spec';

                $c->set(sprintf('locator.locators.%s_suite', $name),
                    function($c) use($srcNS, $specNS, $srcPath, $specPath) {
                        return new PSR0Locator($srcNS, $specNS, $srcPath, $specPath);
                    }
                );
            }
        });

        $app->run($input);
        chdir($pwd);
    }


    /**
     * {@inheritdoc}
     */
    public function getConfigurationFileName()
    {
        return 'phpspec.yml';
    }


    /**
     * {@inheritdoc}
     */
    public function getSpecFile($class)
    {
        $suites = $this->getConfig('suites', []);
        foreach ($suites as $suite)
        {
            $expected_namespaces = [];
            if (! empty($suite['psr4_prefix']))
                $expected_namespaces[] = $suite['psr4_prefix'];
            if (! empty($suite['namespace']))
                $expected_namespaces[] = $suite['namespace'];
            $expected_namespace = join('\\', $expected_namespaces);

            if (strpos($class, $expected_namespace) === 0)
            {
                $path = [$this->getWorkspace()];
                $namespace = [];

                if (! empty($suite['spec_path']))
                    $path[] = empty($suite['spec_path']);
                
                if (empty($suite['spec_prefix']))
                    $suite['spec_prefix'] = 'spec';
                $namespace[] = $suite['spec_prefix'];
                $path[] = str_replace('\\', DS, $suite['spec_prefix']);

                if (! empty($suite['psr4_prefix']))
                    $namespace[] = $suite['psr4_prefix'];

                if (! empty($suite['namespace']))
                {
                    $namespace[] = $suite['namespace'];
                    $path[] = str_replace('\\', DS, $suite['namespace']);
                }

                $path[] = str_replace('\\', DS, substr($class, strlen($expected_namespace) + 1));
                $classes = explode('\\', substr($class, strlen($expected_namespace) + 1));
                $classname = array_pop($classes) . 'Spec';
                if ($classes)
                    $namespace[] = join('\\', $classes);

                $path = join(DS, $path) . 'Spec.php';
                $namespace = join('\\', $namespace);

                $file = new File($path);
                $file->setNamespace($namespace);
                return $file;
            }
        }

        throw new NotFoundException('not found spec available suite.');
    }
}

