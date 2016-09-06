<?php

namespace spec\Samurai\Samurai\Component\Helper;

use Samurai\Samurai\Component\Helper\HtmlHelper;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class HtmlHelperSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HtmlHelper::class);
    }


    public function it_makes_stylesheet_tag()
    {
        $this->stylesheet('http://example.jp/css/some.css')
            ->make()
            ->shouldBe('<link rel="stylesheet" href="http://example.jp/css/some.css" />');
    }

    public function it_makes_script_tag()
    {
        $this->script('http://example.jp/scripts/some.js')
            ->make()
            ->shouldBe('<script type="text/javascript" src="http://example.jp/scripts/some.js"></script>');
    }
    
    public function it_makes_img_tag()
    {
        $this->img('http://example.jp/images/some.jpg')
            ->make()
            ->shouldBe('<img src="http://example.jp/images/some.jpg" />');
    }
    
    public function it_makes_a_tag()
    {
        $this->link('http://example.jp/foo/bar/zoo', 'title', ['target' => '_blank'])
            ->make()
            ->shouldBe('<a target="_blank" href="http://example.jp/foo/bar/zoo">title</a>');
    }

    public function it_makes_h_series_tag()
    {
        $this->h1('heading text')
            ->make()
            ->shouldBe('<h1>heading text</h1>');
        $this->h2('heading text')
            ->make()
            ->shouldBe('<h2>heading text</h2>');
        $this->h3('heading text')
            ->make()
            ->shouldBe('<h3>heading text</h3>');
    }
}

