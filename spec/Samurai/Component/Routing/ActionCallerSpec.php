<?php

namespace spec\Samurai\Samurai\Component\Routing;

use Samurai\Samurai\Component\Routing\ActionCaller;
use Samurai\Samurai\Controller\Controller;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ActionCallerSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ActionCaller::class);
    }

    public function it_is_closure_action()
    {
        $this->byClosure(function(){
            return 'foo';
        });
        $this->execute()->shouldBe('foo');
    }
    
    public function it_this_variable_in_closure_is_samurai_controller()
    {
        $this->byClosure(function(){
            return $this;
        });
        $this->execute()->shouldHaveType(Controller::class);
    }

    public function it_is_classmethod_action()
    {
        $this->byClassMethod('spec\Samurai\Samurai\Component\Routing\ClassMethodController', 'indexAction');
        $this->execute()->shouldBe('bar');
    }

    public function it_is_callable_action()
    {
        $this->byCallable([new ClassMethodController(), 'indexAction']);
        $this->execute()->shouldBe('bar');
    }
}

class ClassMethodController
{
    public function indexAction()
    {
        return 'bar';
    }
}

