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

namespace Samurai\Samurai\Component\Spec\Context;

use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Raikiri\Container\NullableContainer;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Raikiri\Container;

/**
 * PHPSpec text cace context.
 *
 * @package     Samurai
 * @subpackage  Component.Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PHPSpecContext extends ObjectBehavior
{
    /**
     * container for context
     *
     * @var     Samurai\Raikiri\Container
     */
    public $container;

    /**
     * container for bridge to original
     *
     * @var     Samurai\Raikiri\Container
     */
    protected $__container;


    /**
     * set container.
     *
     * @param   Samurai\Raikiri\Container   $container
     */
    public function __setContainer(Container $container)
    {
        $this->__container = $container;
    }

    /**
     * get container
     *
     * @return  Samurai\Raikiri\Container
     */
    public function __getContainer()
    {
        return $this->__container;
    }


    /**
     * skip example
     */
    public function skipExample($message)
    {
        throw new SkippingException($message);
    }



    /**
     * {@inheritdoc}
     */
    public function getWrappedObject()
    {
        try {
            $object = $this->object->getWrappedObject();
            if ($this->container && ! $object->getContainer()) {
                $object->setContainer($this->container);
            }
            return $object;
        } catch (\Exception $e) {
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function __call($method, array $arguments = array())
    {
        if (! in_array(strtolower($method), ['beconstructedwith', 'beconstructedthrough'])) {
            $obj = $this->getWrappedObject();
        }
        return parent::__call($method, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($property, $value)
    {
        $obj = $this->getWrappedObject();
        parent::__set($property, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($property)
    {
        $obj = $this->getWrappedObject();
        return parent::__get($property);
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $obj = $this->getWrappedObject();
        return call_user_func_array('parent::__invoke', func_get_args());
    }
}

