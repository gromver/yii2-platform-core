<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 14.07.15
 * Time: 11:39
 */

namespace gromver\platform\core\components;


use yii\base\InvalidParamException;
use yii\caching\Cache;
use yii\di\Instance;

class ParamsManager {
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

    protected function fetchParamsInfo()
    {

    }

    public function __get($name)
    {
        return $this->params($name);
    }

    /**
     * @param $name
     * @return ParamsObject|null
     */
    public function params($name)
    {
        if (!array_key_exists($name, $this->_paramsInfo)) {
            throw new InvalidParamException('Getting not supported params: ' . get_class($this) . '::' . $name);
        }

        if (!isset($this->_params[$name])) {
            $params = include($this->paramsFilePath($name));
            /** @var ParamsObject $paramsClass */
            $paramsClass = $this->_paramsInfo[$name]['class'];

            $this->_params[$name] = $paramsClass::create(is_array($params) ? $params : []);
        }

        return $this->_params[$name];
    }

    /**
     * @return array
     */
    public function getParamsInfo()
    {
        return $this->_paramsInfo;
    }

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

    public function paramsFilePath($name)
    {
        $language = \Yii::$app->language;

        return \Yii::getAlias("@app/config/grom/params/{$name}-{$language}.php");
    }
} 