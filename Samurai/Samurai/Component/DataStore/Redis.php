<?php

namespace Samurai\Samurai\Component\DataStore;

use Redis as ExtRedis;

/**
 * bridge to data store redis.
 *
 * @package     Samurai.Samurai
 * @subpackage  Component.DataStore
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 */
class Redis
{
    /**
     * driver
     *
     * @var     Redis
     */
    public $driver;

    /**
     * host
     *
     * @var     string
     */
    public $host;

    /**
     * port
     *
     * @var     int
     */
    public $port = 6379;

    /**
     * construct
     */
    public function __construct()
    {
        if (! $this->isSupports()) throw new \Exception('install redis, please.  http://pecl.php.net/package/redis');
        $this->driver = new ExtRedis();
    }

    /**
     * set server
     *
     * @param   string $host
     * @param   int    $port
     */
    public function setServer($host, $port = 6379)
    {
        $this->host = $host;
        $this->port = $port ? $port : $this->port;
    }

    /**
     * connect to redis
     *
     * @param   string $host
     * @param   int    $port
     */
    public function connect($host = null, $port = null)
    {
        $res = $this->driver->connect(
            $host ? $host : $this->host,
            $port ? $port : $this->port
        );
        return $res;
    }

    /**
     * set string
     *
     * @param   string     $key
     * @param   string|int $value
     * @param   int $timeout
     */
    public function set($key, $value, $timeout = 0)
    {
        $res = $this->driver->set($key, (string)$value, $timeout);
        var_dump('set', $key, $value, $res);
    }

    /**
     * get string
     *
     * @param   string $key
     * @return  string
     */
    public function get($key)
    {
        $value = $this->driver->get($key);
        var_dump('get', $key, $value);

        return $value !== false ? $value : null;
    }

    /**
     * add data to list
     *
     * @param   string     $key
     * @param   string|int $value
     */
    public function addList($key, $value)
    {
        $this->driver->rPush($key, (string)$value);
    }

    /**
     * get list
     *
     * @param  string $key
     * @param  int $offset
     * @param  int $limit
     * @return array
     */
    public function getList($key, $offset = 0, $limit = - 1)
    {
        return $this->driver->lRange($key, $offset, $limit);
    }

    /**
     * add data to sets
     *
     * @param   string     $key
     * @param   string|int $value
     */
    public function addSet($key, $value)
    {
        $this->driver->sAdd($key, $value);
    }

    /**
     * get set
     *
     * @param   string
     * @return  array
     */
    public function getSet($key)
    {
        return $this->driver->sMembers($key);
    }

    /**
     * get score.
     *
     * @param  string $key
     * @param  string $member
     * @return float
     */
    public function getScore($key, $member)
    {
        return $this->driver->zScore($key, $member);
    }

    /**
     * add data to sorted set.
     *
     * @param   string    $key
     * @param   string    $member
     * @param   int|float $value
     */
    public function addSortedSet($key, $member, $value)
    {
        $this->driver->zAdd($key, $value, $member);
    }

    /**
     * get sorted set rank
     *
     * @param  string $key
     * @param  string $member
     * @return int|null
     */
    public function getSortedRank($key, $member)
    {
        return $this->getSortedRankAsc($key, $member);
    }

    /**
     * get sorted set rank by asc
     *
     * @param  string $key
     * @param  string $member
     * @return int|null
     */
    public function getSortedRankAsc($key, $member)
    {
        $score = $this->getScore($key, $member);
        if ($score === false) return null;

        return $this->driver->zCount($key, '-inf', -- $score);
    }

    /**
     * get sorted set rank by desc
     *
     * @param  string $key
     * @param  string $member
     * @return int|null
     */
    public function getSortedRankDesc($key, $member)
    {
        $score = $this->getScore($key, $member);
        if ($score === false) return null;

        return $this->driver->zCount($key, ++ $score, '+inf');
    }

    /**
     * get sets with score.
     *
     * @param  string $key
     * @param  int    $start
     * @param  int    $limit
     * @param  bool   $with_scores
     * @return array
     */
    public function getSortedSets($key, $start = 0, $limit = 20, $with_scores = true)
    {
        return $this->getSortedSetsAsc($key, $start, $limit, $with_scores);
    }

    /**
     * get sets with score, order by score asc.
     *
     * @param  string $key
     * @param  int    $start
     * @param  int    $limit
     * @param  bool   $with_scores
     * @return array
     */
    public function getSortedSetsAsc($key, $start = 0, $limit = 20, $with_scores = true)
    {
        return $this->driver->zRangeByScore($key, 0, '+inf', ['limit' => [$start, $limit], 'withscores' => $with_scores]);
    }

    /**
     * get sets with score, order by score desc.
     *
     * @param  string $key
     * @param  int    $start
     * @param  int    $limit
     * @param  bool   $with_scores
     * @return array
     */
    public function getSortedSetsDesc($key, $start = 0, $limit = 20, $with_scores = true)
    {
        return $this->driver->zRevRangeByScore($key, '+inf', 0, ['limit' => [$start, $limit], 'withscores' => $with_scores]);
    }

    /**
     * delete data
     *
     * @param   string $key
     */
    public function delete($key)
    {
        $this->driver->delete($key);
    }


    /**
     * has ?
     *
     * @param   string  $key
     * @return  boolean
     */
    public function has($key)
    {
        return $this->driver->exists($key);
    }


    /**
     * is redis supported ?
     *
     * @return  boolean
     */
    public function isSupports()
    {
        return extension_loaded('redis');
    }
}
