<?php

namespace spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Routing\Rule\GroupRule;
use Samurai\Samurai\Component\Routing\Router;
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
            $group->get('/user/index', function(){});
        });

        $this->matching('/foo/user/index', 'GET')->shouldBe(true);
        $this->matching('/bar/user/index', 'GET')->shouldBe(false);
    }

    public function it_is_domain_grouping()
    {
        $this->beConstructedWith(['domain' => 'example.jp'], function($group){
            $group->get('/user/index', function()
            {
                return 'bar';
            });
        });

        $old = empty($_SERVER['SERVER_NAME']) ? null : $_SERVER['SERVER_NAME'];

        $_SERVER['SERVER_NAME'] = 'example.jp';
        $this->matching('/user/index')->shouldBe(true);

        $_SERVER['SERVER_NAME'] = 'example.com';
        $this->matching('/user/index')->shouldBe(false);
        
        $_SERVER['SERVER_NAME'] = $old;
    }
    
    public function it_bridges_to_hhtp_method_routing()
    {
        $this->beConstructedWith(['prefix' => '/foo'], function($group){
            $group->get('/user/index', function(){});
            $group->post('/user/new', function(){});
            $group->put('/user/edit', function(){});
            $group->patch('/user/edit', function(){});
            $group->delete('/user/delete', function(){});
        });

        $this->matching('/foo/user/index', 'GET')->shouldBe(true);
        $this->matching('/foo/user/new', 'POST')->shouldBe(true);
        $this->matching('/foo/user/edit', 'PUT')->shouldBe(true);
        $this->matching('/foo/user/edit', 'PATCH')->shouldBe(true);
        $this->matching('/foo/user/delete', 'DELETE')->shouldBe(true);

        $this->matching('/foo/user/index', 'POST')->shouldBe(false);
    }

    public function it_is_nested_grouping()
    {
        $this->beConstructedWith(['prefix' => '/foo'], function($group){
            $group->group(['prefix' => '/bar'], function($group){
                $group->get('/user/index', function(){});
            });
        });
        
        $this->matching('/foo/bar/user/index', 'GET')->shouldBe(true);
        $this->matching('/foo/bar/zoo/user/index', 'GET')->shouldBe(false);
        $this->matching('/zoo/foo/bar/user/index', 'GET')->shouldBe(false);
    }
}

