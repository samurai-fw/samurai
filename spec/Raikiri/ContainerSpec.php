<?php

namespace spec\Samurai\Raikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Raikiri\Container;
use Samurai\Raikiri\DependencyInjectable;

class ContainerSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('spec');
        
        $dicon = __DIR__ . DS . 'Fixtures/samurai.dicon';
        $this->import($dicon);

    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\Container');
        $this->getName()->shouldBe('spec');
    }


    public function it_registers_component()
    {
        $component = new Standard();
        $this->register('Standard', $component);
        $this->get('Standard')->shouldBe($component);
    }

    public function it_imports_dicon_file()
    {
        $this->has('Standard')->shouldBe(true);
        $this->has('FooBarZoo')->shouldBe(false);
    }


    public function it_gets_component()
    {
        $component = $this->get('Standard');
        $component->shouldHaveType('spec\Samurai\Raikiri\Standard');
        
        $component = $this->get('Strict');
        $component->shouldHaveType('spec\Samurai\Raikiri\Strict');
    }

    public function it_gets_component_constructor_has_some_arguments()
    {
        $component = $this->get('HasArguments');
        $component->shouldHaveType('spec\Samurai\Raikiri\HasArguments');
        $component->standard->shouldBe($this->get('Standard'));
    }

    public function it_gets_component_prototype()
    {
        $component = $this->get('Prototype');
        $component->shouldHaveType('spec\Samurai\Raikiri\Prototype');
        
        $component2 = $this->get('Prototype');
        $component2->shouldHaveType('spec\Samurai\Raikiri\Prototype');

        $component->shouldNotBe($component2);
    }

    public function it_gets_component_has_initialize_method()
    {
        $component = $this->get('HasInitializeMethod');
        $component->shouldHaveType('spec\Samurai\Raikiri\HasInitializeMethod');
        
        $component = $this->get('HasInitializeMethodWithArguments');
        $component->shouldHaveType('spec\Samurai\Raikiri\HasInitializeMethod');
        $component->standard->shouldBe($this->get('Standard'));
    }


    public function it_has_container_get_from_container()
    {
        $component = $this->get('Injectable');
        $component->getContainer()->shouldHaveType('Samurai\Raikiri\Container');
    }

    public function it_gets_component_define()
    {
        $this->getComponentDefine()->shouldHaveType('Samurai\Raikiri\ComponentDefine');
    }


    public function it_injects_dependencies()
    {
        $strict = $this->get('Strict');
        $strict->Standard->shouldBe($this->get('Standard'));
    }


    public function it_registers_closure()
    {
        $arg1 = 1;
        $arg2 = 2;
        $this->register('some.closuer', function($c) use($arg1, $arg2) {
            $instance = new HasInitializeMethod();
            $instance->initializeWithArguments($arg1, $arg2, $c->get('Standard'));
            return $instance;
        });
        $this->get('some.closuer')->shouldHaveType('spec\Samurai\Raikiri\HasInitializeMethod');
    }


    public function it_inherits(Container $c)
    {
        $c->getAll()->willReturn(['inherit' => (object)['a' => 1, 'b' => 2]]);
        $this->inherit($c);
        $this->get('inherit')->a->shouldBe(1);
        $this->get('inherit')->b->shouldBe(2);
    }
}


/**
 * dummy classes
 */
class Standard
{
}

class Strict
{
    public $Standard;
}

class HasArguments
{
    public $standard;

    public function __construct($arg1, $arg2, Standard $standard)
    {
        $this->standard = $standard;
    }
}

class Prototype
{
}

class HasInitializeMethod
{
    public $standard;

    public function initialize()
    {
    }

    public function initializeWithArguments($arg1, $arg2, Standard $standard)
    {
        $this->standard = $standard;
    }
}

class Injectable
{
    use DependencyInjectable;
}

