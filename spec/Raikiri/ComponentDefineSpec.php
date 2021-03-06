<?php

namespace spec\Samurai\Raikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use PhpSpec\Loader\StreamWrapper;

require_once StreamWrapper::wrapPath(__DIR__ . DS . 'ContainerSpec.php');

class ComponentDefineSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith(['class' => 'spec\Samurai\Raikiri\Standard']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\ComponentDefine');
        $this->getInstance()->shouldHaveType('spec\Samurai\Raikiri\Standard');
    }

    public function it_is_singleton()
    {
        $this->isSingleton()->shouldBe(true);
        
        $instance1 = $this->getInstance();
        $instance2 = $this->getInstance();
        $instance1->shouldBe($instance2);
    }

    public function it_is_prototype()
    {
        $this->beConstructedWith([
            'class' => 'spec\Samurai\Raikiri\Prototype',
            'type' => 'prototype',
        ]);

        $this->isPrototype()->shouldBe(true);

        $instance1 = $this->getInstance();
        $instance2 = $this->getInstance();
        $instance1->shouldNotBe($instance2);
    }

    public function it_constructor_has_arguments(Standard $standard)
    {
        $this->beConstructedWith([
            'class' => 'spec\Samurai\Raikiri\HasArguments',
            'args' => [1, 2, $standard]
        ]);
        
        $instance = $this->getInstance();
        $instance->shouldHaveType('spec\Samurai\Raikiri\HasArguments');
        $instance->standard->shouldBe($standard);
    }

    public function it_has_initialize_method()
    {
        $this->beConstructedWith([
            'class' => 'spec\Samurai\Raikiri\HasInitializeMethod',
            'initMethod' => 'initialize',
        ]);

        $this->getInstance()->shouldHaveType('spec\Samurai\Raikiri\HasInitializeMethod');
    }

    public function it_has_initialize_method_with_arguments(Standard $standard)
    {
        $this->beConstructedWith([
            'class' => 'spec\Samurai\Raikiri\HasInitializeMethod',
            'initMethod' => ['name' => 'initializeWithArguments', 'args' => [1,2, $standard]],
        ]);

        $instance = $this->getInstance();
        $instance->shouldHaveType('spec\Samurai\Raikiri\HasInitializeMethod');
        $instance->standard->shouldBe($standard);
    }
}

