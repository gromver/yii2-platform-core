<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\sql\events;


use gromver\modulequery\Event;

/**
 * Class SqlIndexEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SqlIndexEvent extends Event {
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;
    /**
     * @var \gromver\platform\core\modules\search\modules\sql\models\Index
     */
    public $index;
} 