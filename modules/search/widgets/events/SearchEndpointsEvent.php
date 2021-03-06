<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\widgets\events;


use gromver\modulequery\Event;

/**
 * Class SearchEndpointsEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender null, инициатор \gromver\platform\core\modules\search\widgets\SearchFormFrontend или \gromver\platform\core\modules\search\widgets\SearchFormBackend
 */
class SearchEndpointsEvent extends Event {
    /**
     * @var string[]
     */
    public $items;
}