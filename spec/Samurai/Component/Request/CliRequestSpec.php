<?php

namespace spec\Samurai\Samurai\Component\Request;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class CliRequestSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Request\CliRequest');
    }

    public function it_initialize_argv_to_params()
    {
        $this->init(['argv' => ['./app', '--key1=value1', '--key2=value2', '--multi=1', '--multi=2']]);

        // get values
        $this->get('key1')->shouldBe('value1');
        $this->get('key2')->shouldBe('value2');
        $this->get('multi')->shouldBe('1');

        // get values as array
        $this->getAsArray('key1')->shouldBeArray();
        $this->getAsArray('multi')->shouldBeArray();
        $this->getAsArray('multi')->shouldBe(['1', '2']);
    }

    public function it_gets_script_name()
    {
        // backup.
        $argv = $_SERVER['argv'];

        // sample
        $_SERVER['argv'] = ['./app', '--key1=value1', '--key2=value2', '--multi=1', '--multi=2'];

        $this->init();

        $this->getScriptName()->shouldBe('./app');

        // restore
        $_SERVER['argv'] = $argv;
    }

    public function it_gets_environment_variable()
    {
        $value = $this->getEnv('SAMURAI_SPEC_FOO_BAR_ZOO');
        $value->shouldBe(null);

        $value = $this->getEnv('SAMURAI_SPEC_FOO_BAR_ZOO', 'HOGE');
        $value->shouldBe('HOGE');

        putenv('SAMURAI_SPEC_FOO_BAR_ZOO=HAGE');
        $value = $this->getEnv('SAMURAI_SPEC_FOO_BAR_ZOO');
        $value->shouldBe('HAGE');

        // clean
        putenv('SAMURAI_SPEC_FOO_BAR_ZOO=');
    }
}

