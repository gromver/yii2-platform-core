<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use Yii;

/**
 * Class Module
 * Этот модуль используется админкой для авторизации пользователя, можно настроить период запоминания пользователя в куках,
 * количесвто безуспешных попыток авторизации с последущим подключением капчи
 *
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\core\modules\auth\controllers';
    /**
     * @var int
     * @desc Remember Me Time (seconds), default = 2592000 (30 days)
     */
    public $rememberMeTime = 2592000;           // 30 дней
    public $passwordResetTokenExpire = 3600;    // час
    public $attemptsBeforeCaptcha = 3;          // Unsuccessful Login Attempts before Captcha
    public $authLayout = '@gromver/platform/core/modules/auth/views/layouts/auth';                // если null то применится макет приложения
    // mail
    public $mailer = 'mailer';
    public $emailLayoutPasswordResetToken = '@gromver/platform/core/modules/auth/views/emails/passwordResetToken';
    // todo captcha settings
    public $captchaConfig = [
        'class' => 'yii\captcha\CaptchaAction',
        'transparent' => true,
    ];

    public function init()
    {
        if ($this->authLayout) {
            Yii::$app->layout = $this->authLayout;
        }
    }

    /**
     * @param $event \gromver\platform\core\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Auth'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Login'), 'route' => 'auth/default/login'],
                ['label' => Yii::t('gromver.platform', 'Signup'), 'route' => 'auth/default/signup'],
                ['label' => Yii::t('gromver.platform', 'Request password reset token'), 'route' => 'auth/default/request-password-reset-token'],
                ['label' => Yii::t('gromver.platform', 'Reset password'), 'route' => 'auth/default/reset-password'],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Auth'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Login'), 'url' => ['/auth/default/login']],
                ['label' => Yii::t('gromver.platform', 'Password Reset'), 'url' => ['/auth/default/request-password-reset-token']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes'
        ];
    }
}
