<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\components\events;


use gromver\modulequery\Event;

/**
 * Class FetchParamsEvent
 * Сбор маршрутизаторов с модулей
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\components\ParamsManager $sender
 */
class FetchParamsEvent extends Event {
    /**
     * @var array   [
     *      "ParamsClass1",
     *      "ParamsClass2",
     *      [
     *          'class' => "ParamsClass3",
     *          'rule' => ['setupParamsClass3']
     *      ],
     *      ...
     * ]
     */
    public $items = [];
}