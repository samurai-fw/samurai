<?php

namespace spec\Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\FileSystem\Directory;

class HelperSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Helper');
    }


    public function it_is_name_strategy()
    {
        $this->nameStrategy('test_foo')->shouldBe('TestFoo');
        $this->nameStrategy('test-foo')->shouldBe('TestFoo');
        $this->nameStrategy('TestFoo_bar-zoo')->shouldBe('TestFooBarZoo');
    }


    public function it_is_file_name_strategy()
    {
        $this->fileNameStrategy('base', 'TestFoo')->shouldMatch('/^base\/[0-9]{14}_test_foo.php$/');
    }
    
    public function it_is_class_name_strategy()
    {
        $this->classNameStrategy('base', 'TestFoo')->shouldBe('TestFoo');
    }

    public function it_is_namespace_strategy(Directory $dir)
    {
        $dir->getNameSpace()->willReturn('Sample\\Namespace');
        $this->namespaceStrategy($dir, 'base', 'TestFoo')->shouldBe('');
    }
}

