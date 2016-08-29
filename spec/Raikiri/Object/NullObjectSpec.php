<?php

namespace spec\Samurai\Raikiri\Object;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class NullObjectSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\Object\NullObject');
    }

    public function it_is_getable_undefined_members()
    {
        $this->undefined->shouldHaveType('Samurai\Raikiri\Object\NullObject');
    }

    public function it_is_callable_undefined_method()
    {
        $this->undefined()->shouldHaveType('Samurai\Raikiri\Object\NullObject');
    }

    public function it_is_eacheable()
    {
        $this->shouldImplement('IteratorAggregate');
    }
}

