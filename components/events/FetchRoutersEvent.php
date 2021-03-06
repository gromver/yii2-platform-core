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
 * Class FetchRoutersEvent
 * Сбор маршрутизаторов с модулей
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\components\MenuUrlRule $sender
 */
class FetchRoutersEvent extends Event {
    /**
     * @var array   ["RouterClass1", "RouterClass2", ...]
     */
    public $routers;
}