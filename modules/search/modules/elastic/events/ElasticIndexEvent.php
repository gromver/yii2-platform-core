<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\elastic\events;


use gromver\modulequery\Event;

/**
 * Class ElasticIndexEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\modules\search\modules\elastic\Module $sender
 */
class ElasticIndexEvent extends Event {
    /**
     * @var \yii\db\ActiveRecord
     */
    public $model;
    /**
     * @var \gromver\platform\core\modules\search\modules\elastic\models\Index
     */
    public $index;
} 