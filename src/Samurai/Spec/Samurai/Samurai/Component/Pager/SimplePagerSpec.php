<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Pager;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SimplePagerSpec extends PHPSpecContext
{
    public function let()
    {
        $this->setTotal(101);
        $this->setPerPage(10);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Pager\SimplePager');
    }


    public function it_gets_pages()
    {
        $this->pages()->shouldBe([1,2,3,4,5,6,7,8,9,10,11]);
    }


    public function it_is_last_page()
    {
        $this->last()->shouldBe(11);
    }


    public function it_is_prev_page()
    {
        $this->setNow(1);
        $this->prev()->shouldBe(null);
        
        $this->setNow(2);
        $this->prev()->shouldBe(1);
    }


    public function it_is_next_page()
    {
        $this->setNow(1);
        $this->next()->shouldBe(2);
        
        $this->setNow($this->last());
        $this->next()->shouldBe(null);
    }


    public function it_gets_sliding_paging()
    {
        $this->sliding(3)->shouldBe([1,2,3]);

        $this->setNow(2);
        $this->sliding(3)->shouldBe([1,2,3]);
        
        $this->setNow(3);
        $this->sliding(3)->shouldBe([2,3,4]);
        
        $this->setNow(11);
        $this->sliding(3)->shouldBe([9,10,11]);

        $this->setNow(3);
        $this->sliding(2)->shouldBe([3,4]);
        
        $this->sliding(12)->shouldBe([1,2,3,4,5,6,7,8,9,10,11]);
    }
}

