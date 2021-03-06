<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\console\components;


use yii\web\IdentityInterface;

/**
 * Class User
 * Фейковый компонент для консольных команд, авторизирует первого пользователя из БД
 *
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class User extends \gromver\platform\core\components\User {
    public function init()
    {
        parent::init();

        /* @var $class \gromver\platform\core\modules\user\models\User */
        $class = $this->identityClass;
        /* @var $identity IdentityInterface */
        $identity = $class::find()->orderBy(['id' => SORT_ASC])->one();

        $this->setIdentity($identity);
    }
} 