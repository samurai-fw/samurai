<?php

namespace spec\Samurai\Raikiri\Container;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class NullableContainerSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\Container\NullableContainer');
    }

    public function it_get_undefined_component()
    {
        $this->get('undefined')->shouldHaveType('Samurai\Raikiri\Object\NullObject');
    }
}

