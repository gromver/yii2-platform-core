<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\models\events;
use gromver\modulequery\Event;

/**
 * Class BeforeRolesSaveEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender \gromver\platform\core\modules\user\models\User
 */
class BeforeRolesSaveEvent extends Event {}