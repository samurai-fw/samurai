<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Mapping;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class DefaultNamingStrategySpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Mapping\DefaultNamingStrategy');
    }


    public function it_convert_alias_to_table_class_name()
    {
        $this->aliasToTableClassName('User')->shouldBe('UserTable');
        $this->aliasToTableClassName('user')->shouldBe('UserTable');
        
        $this->aliasToTableClassName('UserPost')->shouldBe('UserPostTable');
        $this->aliasToTableClassName('user_post')->shouldBe('UserPostTable');
    }
    
    
    public function it_convert_alias_to_entity_class_name()
    {
        $this->aliasToEntityClassName('User')->shouldBe('User');
        $this->aliasToEntityClassName('user')->shouldBe('User');
        
        $this->aliasToEntityClassName('UserPost')->shouldBe('UserPost');
        $this->aliasToEntityClassName('user_post')->shouldBe('UserPost');
    }
}

