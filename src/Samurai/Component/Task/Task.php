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

namespace Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Core\Accessor;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Exception\NotImplementsException;
use ReflectionMethod;

/**
 * task class.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Task
{
    /**
     * @traits
     */
    use Accessor;
    use DependencyInjectable;

    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * args
     *
     * @var     array
     */
    public $args = [];

    /**
     * options
     *
     * @var     array
     */
    public $options = [];

    /**
     * list
     *
     * @var     TaskList
     */
    public $list;

    /**
     * method.
     *
     * @var     ReflectionMethod
     */
    public $method = null;


    /**
     * construct
     *
     * @param   TaskList            $list
     * @param   ReflectionMethod    $method
     */
    public function __construct(TaskList $list, ReflectionMethod $method)
    {
        $this->list = $list;
        $this->method = $method;
    }


    /**
     * get name
     *
     * @return  string
     */
    public function getName()
    {
        return sprintf('%s:%s', $this->list->getName(), substr($this->method->getName(), 0, -4));
    }
    
    /**
     * get short description
     *
     * @return  string
     */
    public function getShortDescription()
    {
        $comments = preg_split("/(\n|\r|\r\n)/", $this->method->getDocComment());

        foreach ($comments as $comment) {
            $comment = trim($comment);
            if ($comment === '/**') continue;

            return preg_replace('/^\s*\*\s*/', '', $comment);
        }

        return '';
    }
    
    
    /**
     * execute this task pre setted.
     *
     * @param   array|Samurai\Samurai\Component\Task\Option $option
     */
    public function execute($options = [])
    {
        if (! is_array($options) && ! $options instanceof Option) throw new \InvalidArgumentException('invalid option');

        $option = $this->getOption();
        if (is_array($options)) {
            $option->importFromArray($options);
        } else {
            $option->import($options);
        }
        $option->validate();

        $this->method->invoke($this->list, $option);
    }


    /**
     * get option
     *
     * @return  Samurai\Samurai\Component\Task\Option
     */
    public function getOption()
    {
        $option = new Option();
        $method = $this->getReflection();

        $comment = $method->getDocComment();
        $parser = new OptionParser();
        $lines = [];
        foreach (preg_split('/\r\n|\n|\r/', $comment) as $line) {
            // /** or */ is skip.
            if (in_array(trim($line), ['/**', '*/', '**/'])) continue;

            $line = preg_replace('/^\s*?\*\s?/', '', $line);

            // options
            if($parser->isSupports($line)) {
                $option->addDefinition($parser->parse($line));
                continue;
            }

            // start char is "@" that is doc comment end signal.
            if (preg_match('/^@\w+/', $line)) continue;

            $lines[] = $line;
        }

        if ($options = $option->getDefinitions()) {
            $lines[] = $parser->formatter($option);
        }

        $option->setDescription(join(PHP_EOL, $lines));

        return $option;
    }


    /**
     * get reflection instance.
     *
     * @return  Reflection
     */
    public function getReflection()
    {
        return $this->method;
    }
}

