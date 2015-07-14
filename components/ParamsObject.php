<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 14.07.15
 * Time: 12:26
 */

namespace gromver\platform\core\components;


use yii\base\NotSupportedException;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class ParamsObject extends Object {
    public static function paramsName()
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    public static function create($params)
    {
        $object = new static;
        foreach ($params as $name => $value) {
            if (property_exists($object, $name)) {
                $object->{$name} = $value;
            }
        }

        return $object;
    }

    public function save()
    {
        $paramsFilePath = \Yii::$app->paramsManager->paramsFilePath($this->paramsName());
        file_put_contents($paramsFilePath, '<?php return ' . var_export(ArrayHelper::toArray($this), true) . ';');
        @chmod($paramsFilePath, 0775);
    }
} 