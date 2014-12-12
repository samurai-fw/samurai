<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\DataStore;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use PhpSpec\Exception\Example\SkippingException;

class RedisSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $request;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\DataStore\Redis');
    }


    public function it_connects()
    {
        $host = $this->request->getEnv('SAMURAI_SPEC_REDIS_HOST');
        if (! $host) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_HOST".');
        
        $port = $this->request->getEnv('SAMURAI_SPEC_REDIS_PORT', 6379);
        if (! $port) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_PORT".');

        $this->connect($host, $port)->shouldBe(true);
    }


    public function it_sets_key_value_data()
    {
        $this->_connect();
        $this->set('samurai.spec.foo', 'bar');
        $this->get('samurai.spec.foo')->shouldBe('bar');
    }

    public function it_gets_key_value_data()
    {
        $this->_connect();
        $this->set('samurai.spec.foo', 'bar2');
        $this->get('samurai.spec.foo')->shouldBe('bar2');
    }

    public function it_deletes()
    {
        $this->_connect();
        $this->get('samurai.spec.foo')->shouldNotBe(null);

        $this->delete('samurai.spec.foo');
        
        $this->get('samurai.spec.foo')->shouldBe(null);
    }

    public function it_adds_to_list_and_get_list()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addList('samurai.spec.foo', 'value1');
        $this->addList('samurai.spec.foo', 'value2');
        $this->addList('samurai.spec.foo', 'value1');

        $this->getList('samurai.spec.foo')->shouldBe(['value1', 'value2', 'value1']);
    }
    
    public function it_adds_to_set_and_get_set()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addSet('samurai.spec.foo', 'value1');
        $this->addSet('samurai.spec.foo', 'value2');
        $this->addSet('samurai.spec.foo', 'value1');

        $this->getSet('samurai.spec.foo')->shouldNotHaveDiff(['value2', 'value1']);
    }


    public function it_adds_to_sorted_set_and_get()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addSortedSet('samurai.spec.foo', 'user1', 1);
        $this->addSortedSet('samurai.spec.foo', 'user2', 2);
        $this->addSortedSet('samurai.spec.foo', 'user3', 3);
        $this->addSortedSet('samurai.spec.foo', 'user4', 3);
        $this->addSortedSet('samurai.spec.foo', 'user1', 4);

        $this->getSortedRank('samurai.spec.foo', 'user1')->shouldBe(3);
        $this->getSortedRankAsc('samurai.spec.foo', 'user1')->shouldBe(3);
        $this->getSortedRankDesc('samurai.spec.foo', 'user1')->shouldBe(0);

        // same score
        $this->getSortedRank('samurai.spec.foo', 'user3')->shouldBe($this->getSortedRank('samurai.spec.foo', 'user4'));

        // not entried
        $this->getSortedRank('samurai.spec.foo', 'user99')->shouldBe(null);
    }

    /**
     * @todo   should be migrate, fixture load for datastore
     * @throws SkippingException
     */
    public function it_gets_sets_with_scores()
    {
        $key = 'samurai.spec.foo';

        $this->_connect();

        $this->delete($key);
        $this->addSortedSet($key, 'user1', 100);
        $this->addSortedSet($key, 'user2', 2);
        $this->addSortedSet($key, 'user3', 300);
        $this->addSortedSet($key, 'user4', 2147483647);

        $expect = [
            'user2' => 2,
            'user1' => 100,
            'user3' => 300,
            'user4' => 2147483647,
        ];
        $this->getSortedSets($key, 0, 4)->shouldBeLike($expect);
        $this->getSortedSetsAsc($key, 0, 4)->shouldBeLike($expect);

        $expect = [
            'user1' => 100,
            'user3' => 300,
        ];
        $this->getSortedSets($key, 1, 2)->shouldBeLike($expect);
        $this->getSortedSetsAsc($key, 1, 2)->shouldBeLike($expect);

        $expect = [
            'user3' => 300,
            'user1' => 100,
        ];
        $this->getSortedSetsDesc($key, 1, 2)->shouldBeLike($expect);
    }

    /**
     * @todo   should be migrate, fixture load for datastore
     * @throws SkippingException
     */
    public function it_gets_sets_without_scores()
    {
        $key = 'samurai.spec.foo';

        $this->_connect();

        $this->delete($key);
        $this->addSortedSet($key, 'user1', 100);
        $this->addSortedSet($key, 'user2', 2);
        $this->addSortedSet($key, 'user3', 300);
        $this->addSortedSet($key, 'user4', 2147483647);

        $expect = [
            'user2',
            'user1',
            'user3',
            'user4',
        ];
        $this->getSortedSets($key, 0, 4, false)->shouldBeLike($expect);
        $this->getSortedSetsAsc($key, 0, 4, false)->shouldBeLike($expect);

        $expect = [
            'user3',
            'user4',
        ];
        $this->getSortedSets($key, 2, 2, false)->shouldBeLike($expect);
        $this->getSortedSetsAsc($key, 2, 2, false)->shouldBeLike($expect);

        $expect = [
            'user4',
            'user3',
            'user1',
            'user2',
        ];
        $this->getSortedSetsDesc($key, 0, 4, false)->shouldBeLike($expect);

        $expect = [
            'user1',
            'user2',
        ];
        $this->getSortedSetsDesc($key, 2, 2, false)->shouldBeLike($expect);
    }

    public function it_gets_score()
    {
        $key = 'samurai.spec.foo';

        $this->_connect();

        $this->delete($key);
        $this->addSortedSet($key, 'user1', 1);
        $this->addSortedSet($key, 'user2', 2);
        $this->addSortedSet($key, 'user3', 3);
        $this->addSortedSet($key, 'user4', 0);
        $this->addSortedSet($key, 'user1', 4);

        $this->getScore($key, 'user1')->shouldBeLike(4);
        $this->getScore($key, 'user2')->shouldBeLike(2);
        $this->getScore($key, 'user3')->shouldBeLike(3);
        $this->getScore($key, 'user4')->shouldBeLike(0);
    }

    /**
     * connect to redis
     */
    private function _connect()
    {
        $host = $this->request->getEnv('SAMURAI_SPEC_REDIS_HOST');
        if (! $host) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_HOST".');
        
        $port = $this->request->getEnv('SAMURAI_SPEC_REDIS_PORT', 6379);
        if (! $port) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_PORT".');

        $this->connect($host, $port);
    }


    /**
     * matchers
     */
    public function getMatchers()
    {
        return [
            'haveDiff' => function($subject, $expect) {
                return array_diff($subject, $expect) || count($subject) !== count($expect);
            }
        ];
    }
}

