<?php

namespace spec\Samurai\Samurai\Component\Helper;

use Samurai\Samurai\Component\Helper\Tag;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class TagSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('div');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Tag::class);
    }

    public function it_is_blank_tag()
    {
        $this->make()->shouldBe('<div></div>');
    }

    public function it_has_text_contents()
    {
        $this->setText('some contents text. <b>html tag is escaped</b>');
        $this->make()->shouldBe('<div>some contents text. &lt;b&gt;html tag is escaped&lt;/b&gt;</div>');
    }
    
    public function it_has_html_contents()
    {
        $this->setHTML('some contents html. <b>html tag is not escaped</b>');
        $this->make()->shouldBe('<div>some contents html. <b>html tag is not escaped</b></div>');
    }

    public function it_adds_some_contents()
    {
        $this->addText('it is text. <b>html tag is escaped</b>');
        $this->addHTML('it is html. <b>html tag is not escaped</b>');
        $this->make()->shouldBe('<div>'
            . 'it is text. &lt;b&gt;html tag is escaped&lt;/b&gt;'
            . 'it is html. <b>html tag is not escaped</b>'
            . '</div>');
    }

    public function it_has_attributes()
    {
        $this->setAttribute('name', 'some');
        $this->make()->shouldBe('<div name="some"></div>');
    }

    public function it_adds_some_style_attributes()
    {
        $this->addAttributes('style', 'background-color:#000000');
        $this->addAttributes('style', 'background-position:center middle');
        $this->make()->shouldBe('<div style="background-color:#000000;background-position:center middle"></div>');
    }
    
    public function it_adds_some_class_attributes()
    {
        $this->addAttributes('class', 'class1');
        $this->addAttributes('class', 'class2');
        $this->make()->shouldBe('<div class="class1 class2"></div>');
    }

    public function it_auto_detect_closing_mode_for_br()
    {
        $this->beConstructedWith('br');
        $this->make()->shouldBe('<br />');
    }
    
    public function it_auto_detect_closing_mode_for_link()
    {
        $this->beConstructedWith('link');
        $this->make()->shouldBe('<link />');
    }
    
    public function it_auto_detect_closing_mode_for_form()
    {
        $this->beConstructedWith('form');
        $this->make()->shouldBe('<form>');
    }
}

