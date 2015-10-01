<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\components\MenuUrlRule;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use gromver\platform\core\modules\page\components\MenuRouterPage;
use gromver\platform\core\modules\search\widgets\SearchResultsBackend;
use gromver\platform\core\modules\search\widgets\SearchResultsFrontend;
use gromver\platform\core\modules\page\models\Page;
use gromver\platform\core\modules\search\modules\sql\widgets\SearchResultsFrontend as SqlSearchResults;
use gromver\platform\core\modules\search\modules\elastic\widgets\SearchResultsFrontend as ElasticSearchResults;
use Yii;

/**
 * Class Module
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\core\modules\page\controllers';
    public $defaultRoute = 'frontend/default';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
            SqlSearchResults::EVENT_BEFORE_SEARCH => [Page::className(), 'sqlBeforeFrontendSearch'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH => [Page::className(), 'elasticBeforeFrontendSearch'],
            SearchResultsBackend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsBackend',
            SearchResultsFrontend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsFrontend'
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/page/backend/default/index']]
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Page View'), 'url' => ['/page/backend/default/select']],
                ['label' => Yii::t('gromver.platform', 'Page Guide'), 'url' => ['/page/backend/default/select', 'route' => 'page/frontend/default/guide']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\components\events\FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers[] = MenuRouterPage::className();
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsBackend($event)
    {
        $event->items['gromver\platform\core\modules\page\models\Page'] = Yii::t('gromver.platform', 'Pages');
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsFrontend($event)
    {
        $event->items['gromver\platform\core\modules\page\models\Page'] = Yii::t('gromver.platform', 'Pages');
    }
}
