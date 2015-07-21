<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\components\ParamsManager;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use gromver\platform\core\modules\main\models\MainParams;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property string $siteName
 * @property bool $isEditMode
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = '\gromver\platform\core\modules\main\controllers';
    public $defaultRoute = 'frontend/default';
    /**
     * @var string место хранения настроек сайта
     */
    public $paramsClass = 'gromver\platform\core\modules\main\models\MainParams';

    /*public function initI18N()
    {
        Yii::$app->i18n->translations['gromver.*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@gromver/platform/core/messages',
        ];
    }*/

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'System'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Desktop'), 'url' => ['/main/backend/default/index']],
                ['label' => Yii::t('gromver.platform', 'System Configuration'), 'url' => ['/main/backend/default/params']],
                ['label' => Yii::t('gromver.platform', 'Flush Cache'), 'url' => ['/main/backend/default/flush-cache']],
                ['label' => Yii::t('gromver.platform', 'Flush Assets'), 'url' => ['/main/backend/default/flush-assets']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'System'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Dummy Page'), 'route' => 'main/frontend/default/dummy-page'],
                ['label' => Yii::t('gromver.platform', 'Contact Form'), 'route' => 'main/frontend/default/contact'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            //User::EVENT_BEFORE_USER_ROLES_SAVE => 'beforeUserRolesSave',
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            ParamsManager::EVENT_FETCH_MODULE_PARAMS => 'addParams',
        ];
    }

    /**
     * @param $event \gromver\platform\core\components\events\FetchParamsEvent
     */
    public function addParams($event)
    {
        $event->items[] = MainParams::className();
    }

    /**
     * @param $event \gromver\platform\core\modules\user\models\events\BeforeRolesSaveEvent
     * Всем пользователям всегда устанавливаем роль Authorized
     */
    public function beforeUserRolesSave($event)
    {
        $roles = $event->sender->getRoles();
        if (!in_array('authorized', $roles)) {
            $roles[] = 'authorized';
            $event->sender->setRoles($roles);
        }
    }
}
