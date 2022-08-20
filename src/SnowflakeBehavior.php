<?php

namespace bestyii\snowflake;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * Class SnowflakeBehavior
 */
class SnowflakeBehavior extends AttributeBehavior
{
    /**
     * @var string
     */
    public $attribute = 'id';

    /**
     * @inheritdoc
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->attribute]
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->snowflake->id();
        }
        return parent::getValue($event);
    }
}