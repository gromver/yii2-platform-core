<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\models;


/**
 * Class UserProfile
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class UserProfile
{
    /**
     * @translation gromver.platform
     */
    public $name;
    /**
     * @translation gromver.platform
     */
    public $surname;
    /**
     * @translation gromver.platform
     */
    public $patronymic;
    /**
     * @translation gromver.platform
     */
    public $phone;
    /**
     * @translation gromver.platform
     */
    public $work_phone;
    /**
     * @field text
     * @email
     * @translation gromver.platform
     * @var string
     */
    public $email;
    /**
     * @field text
     * @email
     * @translation gromver.platform
     * @var string
     */
    public $work_email;
    /**
     * @translation gromver.platform
     */
    public $address;
} 