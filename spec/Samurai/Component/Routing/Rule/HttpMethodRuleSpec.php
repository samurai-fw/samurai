<?php

namespace spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Routing\Rule\HttpMethodRule;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class HttpMethodRuleSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HttpMethodRule::class);
    }

    public function it_is_root_matching()
    {
        $this->setPath('/');
        $this->setMethod(HttpMethodRule::HTTP_METHOD_GET);

        $this->match('/', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(true);
        $this->match('/', HttpMethodRule::HTTP_METHOD_POST)->shouldBe(false);
        $this->match('/foo', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
    }
    

    public function it_is_someaction_matching()
    {
        $this->setPath('/user/index');
        $this->setMethod(HttpMethodRule::HTTP_METHOD_GET);

        $this->match('/', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
        $this->match('/user/edit', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
        $this->match('/user/index', HttpMethodRule::HTTP_METHOD_POST)->shouldBe(false);
        $this->match('/user/index', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(true);
    }
}

