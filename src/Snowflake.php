<?php

namespace bestyii\snowflake;

use Godruoyi\Snowflake\SequenceResolver;
use \Godruoyi\Snowflake\Snowflake as BaseSnowflake;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;

class Snowflake extends \yii\base\Component
{
    private $instance;
    public $startDate;
    public $datacenterId;
    public $workerId;
    public $sequencer;

    /**
     * @throws \Exception
     */
    public function init()
    {
        if (is_int($this->datacenterId) && is_int($this->workerId)) {
            $this->instance = new BaseSnowflake($this->datacenterId, $this->workerId);
        } else {
            $this->instance = new BaseSnowflake;
        }
        if ($timestamp = strtotime($this->startDate)) {
            $this->instance->setStartTimeStamp($timestamp * 1000);
        } else {
            throw new InvalidConfigException('The "startDate" property must be date and format is "Y-m-d", Exp. "2022-12-31".');
        }

        if(is_string($this->sequencer)){
            $reference = new $this->sequencer;
            $this->instance->setSequenceResolver($reference);
        }

        if ($this->sequencer !== null && ($this->sequencer instanceof SequenceResolver || is_callable($this->sequencer))) {
            $this->instance->setSequenceResolver($this->sequencer);
        }

        parent::init();
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->instance, $method], $parameters);
    }

    /**
     * @return BaseSnowflake
     */
    public function getInstance()
    {
        return $this->instance;
    }

}