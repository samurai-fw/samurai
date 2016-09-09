<?php

namespace spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Routing\Rule\GroupRule;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class GroupRuleSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith([], function(){});
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(GroupRule::class);
    }


    public function it_is_prefix_grouping()
    {
        $this->beConstructedWith(['prefix' => '/foo'], function($group){
            $group->get('/user/index', function()
            {
                return 'bar';
            });
        });

        $this->match('/foo/user/index')->shouldBe(true);
        $this->match('/bar/user/index')->shouldBe(false);
    }
}

