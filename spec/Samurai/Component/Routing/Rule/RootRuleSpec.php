<?php

namespace spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class RootRuleSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('default.index');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\RootRule');
    }


    public function it_is_match()
    {
        $this->matching('/')->shouldBe(true);
    }

    public function it_is_not_match()
    {
        $this->matching('/foo')->shouldBe(false);;
        $this->matching('/foo/bar/zoo')->shouldBe(false);;
        $this->matching('/favicon.ico')->shouldBe(false);;
    }

    public function it_is_invalid_constructor_arguments()
    {
        $this->beConstructedWith('foo');
        $this->shouldThrow('Samurai\Samurai\Component\Routing\Exception\InvalidArgumentException')->duringInstantiation();
    }
}

