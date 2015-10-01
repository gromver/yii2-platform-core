<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\menu\widgets\events;


use gromver\modulequery\Event;

/**
 * Class MenuItemRoutesEvent
 * См. \gromver\platform\core\modules\menu\widgets\MenuItemRoutes
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuItemRoutesEvent extends Event {
    /**
     * @var array
     */
    public $items;
}