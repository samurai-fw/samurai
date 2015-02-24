<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\DataSource;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class YAMLSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\DataSource\YAML');
    }


    public function it_loads_string()
    {
        $string = <<<EOL
---
foo: bar
names:
    - satoshi
    - minka
EOL;
        $this->load($string)->shouldBe([
            'foo' => 'bar',
            'names' => ['satoshi', 'minka'],
        ]);
    }

    public function it_loads_file()
    {
        $file = __DIR__ . '/Fixtures/sample.yml';
        $this->load($file)->shouldBe([
            'bar' => 'zoo',
            'names' => ['satoshi', 'minka'],
        ]);
    }

    public function it_replace_environment_variables_place_holder()
    {
        putenv('BAR=barbarbar');
        putenv('NAME_1=satoshinosuke');

        $file = __DIR__ . '/Fixtures/sample2.yml';
        $this->load($file)->shouldBeLike([
            'bar' => 'barbarbar',
            'names' => ['satoshinosuke', null, 'minka'],
        ]);
    }
}

