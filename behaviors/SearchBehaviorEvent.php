<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\behaviors;


use gromver\modulequery\Event;

/**
 * Class SearchBehaviorEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchBehaviorEvent extends Event {
    /**
     * @var \yii\db\ActiveRecord|\gromver\platform\core\interfaces\model\ViewableInterface|\gromver\platform\core\interfaces\model\SearchableInterface
     */
    public $model;
} 