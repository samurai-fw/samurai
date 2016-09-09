<?php

namespace spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class MatchRuleSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\MatchRule');
    }


    /**
     * match: { /login: user.login, as login }
     */
    public function it_is_match_standard()
    {
        $this->beConstructedWith([
            '/login' => 'user.login',
            'as' => 'login',
        ]);

        $this->matching('/login')->shouldBe(true);

        $this->getName()->shouldBe('login');
        $this->getController()->shouldBe('user');
        $this->getAction()->shouldBe('login');
    }

    /**
     * match use *
     */
    public function it_is_match_ast()
    {
        $this->beConstructedWith(['/foo/*' => 'foo.compile', 'as' => 'foo']);

        $this->matching('/foo/bar')->shouldBe(true);
        $this->matching('/foo/bar/zoo')->shouldBe(true);
        $this->getController()->shouldBe('foo');
        $this->getAction()->shouldBe('compile');
    }
    
    public function it_is_match_with_params()
    {
        $this->beConstructedWith(['/foo/:bar/:zoo' => 'foo.compile', 'as' => 'foo']);

        $this->matching('/foo/1/2')->shouldBe(true);
        $this->getController()->shouldBe('foo');
        $this->getAction()->shouldBe('compile');
        $this->getParams()->shouldBe(['bar' => '1', 'zoo' => '2']);
    }

    public function it_is_match_with_suffix()
    {
        $this->beConstructedWith(['/photo/:id.:format' => 'photo.show', 'as' => 'photo_show']);

        $this->matching('/photo/123.jpg')->shouldBe(true);
        $this->getController()->shouldBe('photo');
        $this->getAction()->shouldBe('show');
        $this->getParams()->shouldBe(['format' => 'jpg', 'id' => '123']);
    }


    public function it_is_prefix_matching()
    {
        $this->setPath('/user/index');
        $this->prefix('/foo');
        
        $this->matching('/user/index')->shouldBe(false);
        $this->matching('/foo/user/index')->shouldBe(true);
    }

    public function it_converts_to_url_from_method_name()
    {
        $this->methodName2URL('indexAction')->shouldBe('index');
        $this->methodName2URL('fooBarZooAction')->shouldBe('foo-bar-zoo');
        $this->methodName2URL('foo_bar_zooAction')->shouldBe('foo_bar_zoo');
    }
}

