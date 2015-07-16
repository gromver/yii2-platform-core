<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main;


use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleEventsInterface;
use gromver\modulequery\ModuleQuery;
use gromver\platform\core\components\events\FetchParamsEvent;
use gromver\platform\core\components\MenuManager;
use gromver\platform\core\components\ParamsManager;
use gromver\platform\core\modules\main\events\ListItemsModuleEvent;
use gromver\platform\core\modules\main\models\DbState;
use gromver\platform\core\modules\menu\models\MenuItem;
use gromver\platform\core\modules\search\widgets\SearchResultsBackend;
use gromver\platform\core\modules\search\widgets\SearchResultsFrontend;
use gromver\platform\core\modules\user\models\User;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use gromver\platform\core\modules\main\models\MainParams;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\caching\ExpressionDependency;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property string $siteName
 * @property bool $isEditMode
 */
class Module extends \yii\base\Module implements BootstrapInterface, ModuleEventsInterface
{
    public $controllerNamespace = '\gromver\platform\core\modules\main\controllers';
    public $defaultRoute = 'frontend/default';
    /**
     * @var string место хранения настроек сайта
     */
    public $paramsClass = 'gromver\platform\core\modules\main\models\MainParams';

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->set('grom', $this);

        /** @var MainParams $params */
        $params = Yii::$app->paramsManager->main;

        // устанавливает мета описание сайта по умолчанию
        $view = Yii::$app->getView();
        $view->title = $params->siteTitle;
        if (!empty($params->keywords)) {
            $view->registerMetaTag(['name' => 'keywords', 'content' => $params->keywords], 'keywords');
        }
        if (!empty($params->description)) {
            $view->registerMetaTag(['name' => 'description', 'content' => $params->description], 'description');
        }
        if (!empty($params->robots)) {
            $view->registerMetaTag(['name' => 'robots', 'content' => $params->robots], 'robots');
        }
        $view->registerMetaTag(['name' => 'generator', 'content' => 'Grom Platform - Open Source Yii2 Development Platform.'], 'generator');
    }

    /**
     * @inheritdoc
     */
    /*public function init()
    {

    }

    public function initI18N()
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
                //['label' => Yii::t('gromver.platform', 'Sitemap'), 'route' => 'main/frontend/default/sitemap'/*, 'icon' => '<i class="glyphicon glyphicon-cog"></i>'*/],
                ['label' => Yii::t('gromver.platform', 'Dummy Page'), 'route' => 'main/frontend/default/dummy-page'],
                ['label' => Yii::t('gromver.platform', 'Contact Form'), 'route' => 'main/frontend/default/contact'],
            ]
        ];
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return !empty(Yii::$app->paramsManager->main->siteName) ? Yii::$app->paramsManager->main->siteName : Yii::$app->name;
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
            SearchResultsBackend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsBackend',
            SearchResultsFrontend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsFrontend'
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
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsBackend($event)
    {
        $event->items = array_merge($event->items, Yii::$app->backendSearchableModels/*[
            'gromver\platform\core\modules\page\models\Page' => Yii::t('gromver.platform', 'Pages'),
            'gromver\platform\core\modules\news\models\Post' => Yii::t('gromver.platform', 'Posts'),
            'gromver\platform\core\modules\news\models\Category' => Yii::t('gromver.platform', 'Categories'),
        ]*/);
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsFrontend($event)
    {
        $event->items = array_merge($event->items, Yii::$app->frontendSearchableModels/*[
            'gromver\platform\core\modules\page\models\Page' => Yii::t('gromver.platform', 'Pages'),
            'gromver\platform\core\modules\news\models\Post' => Yii::t('gromver.platform', 'Posts'),
            'gromver\platform\core\modules\news\models\Category' => Yii::t('gromver.platform', 'Categories'),
        ]*/);
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
