<?php

namespace bestyii\snowflake;

use Godruoyi\Snowflake\SequenceResolver;
use Yii;
use yii\di\Instance;
use yii\redis\Connection;

class RedisSequenceResolver implements SequenceResolver
{
    /**
     * @var Connection|string|array the Redis [[Connection]] object or the application component ID of the Redis [[Connection]].
     * This can also be an array that is used to create a redis [[Connection]] instance in case you do not want do configure
     * redis connection as an application component.
     * After the Session object is created, if you want to change this property, you should only assign it
     * with a Redis [[Connection]] object.
     */
    public $redis = 'redis';

    /**
     * @var string a string prefixed to every cache key so that it is unique. If not set,
     * it will use a prefix generated from string 'snowflake'. You may set this property to be an empty string
     * if you don't want to use key prefix. It is recommended that you explicitly set this property to some
     * static value if the cached data needs to be shared among multiple applications.
     */
    public $keyPrefix;

    /**
     * Init resolve instance, must connectioned.
     * @throws \yii\base\InvalidConfigException
     */

    public function __construct()
    {
        $this->redis = Instance::ensure($this->redis, Connection::className());
        if ($this->keyPrefix === null) {
            $this->keyPrefix = substr(md5('snowflake'), 0, 5);
        }
    }

    /**
     *  {@inheritdoc}
     */
    public function sequence(int $currentTime)
    {

        $key = $this->keyPrefix . $currentTime;

        if ($this->addValue($key, 1, 10)) {
            return 0;
        }
        // 10 seconds
        return $this->increment($key);
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $expire)
    {
        if ($expire == 0) {
            return (bool)$this->redis->executeCommand('SET', [$key, $value, 'NX']);
        }

        $expire = (int)($expire * 1000);

        return (bool)$this->redis->executeCommand('SET', [$key, $value, 'PX', $expire, 'NX']);
    }

    /**
     * @inheritdoc
     */
    protected function increment($key, $value = 1)
    {
        return (bool)$this->redis->executeCommand('INCRBY', [$key, $value]);

    }
}