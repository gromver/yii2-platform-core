<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 14.07.15
 * Time: 11:39
 */

namespace gromver\platform\core\components;


use gromver\modulequery\ModuleEvent;
use gromver\platform\core\components\events\FetchParamsEvent;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\base\UnknownPropertyException;
use yii\caching\Cache;
use yii\di\Instance;

/**
 * Class ParamsManager
 * @package gromver\platform\core\components
 *
 * @property array $paramsInfo
 */
class ParamsManager extends Object {
    const EVENT_FETCH_MODULE_PARAMS = 'ParamsManagerParams';

    /**
     * @var Cache|string
     */
    public $cache;
    /**
     * @var integer
     */
    public $cacheDuration;
    /**
     * @var \yii\caching\Dependency
     */
    public $cacheDependency;

    private $_params = [];
    private $_paramsInfo;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = __CLASS__;
            if (($this->_paramsInfo = $this->cache->get($cacheKey)) === false) {
                $this->_paramsInfo = $this->fetchParamsInfo();
                $this->cache->set($cacheKey, $this->_paramsInfo, $this->cacheDuration, $this->cacheDependency);
            }
        } else {
            $this->_paramsInfo = $this->fetchParamsInfo();
        }
    }

    /**
     * @return array
     * @throws InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    protected function fetchParamsInfo()
    {
        $items = ModuleEvent::trigger(self::EVENT_FETCH_MODULE_PARAMS, new FetchParamsEvent([
            'sender' => $this
        ]), 'items');

        $result = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                /** @var \gromver\platform\core\components\ParamsObject $item */
                $result[$item::paramsType()] = [
                    'class' => $item
                ];
            } elseif (is_array($item)) {
                /** @var \gromver\platform\core\components\ParamsObject $class */
                $class = $item['class'];
                $result[$class::paramsType()] = $item;
            } else {
                throw new InvalidConfigException(get_class($this) . '::fetchParamsInfo invalid params configuration.');
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @return ParamsObject|mixed|null
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            return $this->params($name);
        }
    }

    /**
     * @param $type string
     * @return ParamsObject|null
     * @throws UnknownPropertyException
     */
    public function params($type)
    {

        if (!isset($this->_params[$type])) {
            if (!array_key_exists($type, $this->_paramsInfo)) {
                throw new UnknownPropertyException('Getting not supported params: ' . get_class($this) . '::' . $type);
            }

            $params = @include($this->paramsFilePath($type));
            /** @var ParamsObject $paramsClass */
            $paramsClass = $this->_paramsInfo[$type]['class'];

            $this->_params[$type] = $paramsClass::create(is_array($params) ? $params : []);
        }

        return $this->_params[$type];
    }

    /**
     * @return array
     */
    public function getParamsInfo()
    {
        return $this->_paramsInfo;
    }

    /**
     * @param null|string|array $cases
     * @throws UnknownPropertyException
     */
    public function save($cases = null)
    {
        if ($cases) {
            $paramsCases = (array)$cases;
        } else {
            $paramsCases = array_keys($this->_paramsInfo);
        }

        foreach ($paramsCases as $name) {
            $this->params($name)->save();
        }
    }

    /**
     * @param $type string
     * @return bool|string
     */
    public function paramsFilePath($type)
    {
        return \Yii::getAlias("@app/config/grom/params/{$type}.php");
    }
} 