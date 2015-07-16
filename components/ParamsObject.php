<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 14.07.15
 * Time: 12:26
 */

namespace gromver\platform\core\components;


use yii\base\ArrayableTrait;
use yii\base\NotSupportedException;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class ParamsObject extends Object {
    use ArrayableTrait;

    /**
     * @throws NotSupportedException
     * @return string
     */
    public static function paramsName()
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * Уникальный идентификатор параметров, используется впоследствии для доступа к параметрам через менеджер параметров
     * Yii::$app->paramsManager->foo
     * @throws NotSupportedException
     * @return string
     */
    public static function paramsType()
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * @param $params
     * @return static
     */
    public static function create($params)
    {
        $object = new static;
        $object->load($params);

        return $object;
    }

    /**
     * @param null|string $language
     * @return $this
     * @throws NotSupportedException
     */
    public function save($language = null)
    {
        $paramsFilePath = \Yii::$app->paramsManager->paramsFilePath($this->paramsType(), $language);
        file_put_contents($paramsFilePath, '<?php return ' . var_export(ArrayHelper::toArray($this), true) . ';');
        @chmod($paramsFilePath, 0775);

        return $this;
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function load($attributes)
    {
        foreach ($attributes as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }

        return $this;
    }
} 