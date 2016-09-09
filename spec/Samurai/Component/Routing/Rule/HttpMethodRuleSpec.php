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

        $this->matching('/', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(true);
        $this->matching('/', HttpMethodRule::HTTP_METHOD_POST)->shouldBe(false);
        $this->matching('/foo', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
    }
    

    public function it_is_someaction_matching()
    {
        $this->setPath('/user/index');
        $this->setMethod(HttpMethodRule::HTTP_METHOD_GET);

        $this->matching('/', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
        $this->matching('/user/edit', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(false);
        $this->matching('/user/index', HttpMethodRule::HTTP_METHOD_POST)->shouldBe(false);
        $this->matching('/user/index', HttpMethodRule::HTTP_METHOD_GET)->shouldBe(true);
    }
    


    public function it_is_http_only()
    {
        $this->setPath('/user/index');
        $this->matching('/user/index')->shouldBe(true);

        $_SERVER['HTTPS'] = 'on';
        $this->matching('/user/index')->shouldBe(true);

        $this->notSecure();
        $this->matching('/user/index')->shouldBe(false);
        
        unset($_SERVER['HTTPS']);
    }
    
    public function it_is_https_only()
    {
        $this->setPath('/user/index');
        $this->matching('/user/index')->shouldBe(true);

        // in secure
        $this->secure();
        $this->matching('/user/index')->shouldBe(false);

        $_SERVER['HTTPS'] = 'on';
        $this->matching('/user/index')->shouldBe(true);
        
        unset($_SERVER['HTTPS']);
    }

    public function it_is_domain_matching()
    {
        $this->setPath('/user/index');
        $this->domain('example.jp');

        $domain = empty($_SERVER['SERVER_NAME']) ? null : $_SERVER['SERVER_NAME'];

        $_SERVER['SERVER_NAME'] = 'example.jp';
        $this->matching('/user/index')->shouldBe(true);

        $_SERVER['SERVER_NAME'] = 'example.com';
        $this->matching('/user/index')->shouldBe(false);
        
        $_SERVER['SERVER_NAME'] = $domain;
    }
    
    public function it_converts_to_url_from_method_name()
    {
        $this->methodName2URL('getIndexAction')->shouldBe('index');
        $this->methodName2URL('getFooBarZooAction')->shouldBe('foo-bar-zoo');
        $this->methodName2URL('getfoo_bar_zooAction')->shouldBe('foo_bar_zoo');

        $this->restful(false);
        $this->methodName2URL('getIndexAction')->shouldBe('get-index');
    }
}

