<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\elastic;


use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\components\MenuUrlRule;
use gromver\platform\core\modules\search\widgets\SearchFormBackend;
use gromver\platform\core\modules\search\widgets\SearchFormFrontend;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use gromver\platform\core\modules\search\modules\elastic\components\MenuRouterSearch;
use gromver\platform\core\modules\search\modules\elastic\events\ElasticIndexEvent;
use gromver\platform\core\modules\search\modules\elastic\models\Index;
use kartik\widgets\Alert;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \gromver\platform\core\modules\search\Module implements ModuleEventsInterface
{
    const EVENT_BEFORE_CREATE_INDEX = 'ElasticsearchBeforeCreateIndex';
    const EVENT_BEFORE_DELETE_INDEX = 'ElasticsearchBeforeDeleteIndex';

    public $controllerNamespace = 'gromver\platform\core\modules\search\modules\elastic\controllers';
    public $defaultRoute = 'frontend/default';
    public $elasticIndex;

    public function init()
    {
        parent::init();

        if (!isset($this->elasticIndex)) {
            throw new InvalidConfigException(__CLASS__ . '::elasticIndex must be set.');
        }
    }

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Elastic Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'url' => ['/' . $this->getUniqueId() . '/backend/default/index']],
                ['label' => Yii::t('gromver.platform', 'Reindex'), 'url' => ['/' . $this->getUniqueId() . '/backend/default/reindex']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Elastic Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'route' => $this->getUniqueId() . '/frontend/default/index'],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\components\events\FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers[] = MenuRouterSearch::className();
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchEndpointsEvent
     */
    public function addSearchEndpointFrontend($event)
    {
        $event->items['/' . $this->getUniqueId() . '/frontend/default/index'] = Yii::t('gromver.platform', 'Elastic Search');
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchEndpointsEvent
     */
    public function addSearchEndpointBackend($event)
    {
        $event->items['/' . $this->getUniqueId() . '/backend/default/index'] = Yii::t('gromver.platform', 'Elastic Search');
    }

    /**
     * @inheritdoc
     */
    public function indexPage($event)
    {
        $index = Index::findOne(['model_id' => $event->model->getPrimaryKey(), 'model_class' => $event->model->className()]) or $index = new Index();
        $index->model_id = $event->model->getPrimaryKey();
        $index->model_class = $event->model->className();
        $index->title = $event->model->getSearchTitle();
        $index->content = $event->model->getSearchContent();
        $index->tags = $event->model->getSearchTags();
        $index->url_backend = $event->model->getBackendViewLink();
        $index->url_frontend = $event->model->getFrontendViewLink();

        ModuleEvent::trigger(self::EVENT_BEFORE_CREATE_INDEX, new ElasticIndexEvent([
            'model' => $event->model,
            'index' => $index,
            'sender' => $this,
        ]));

        if (!$index->save()) {
            Yii::$app->session->setFlash(Alert::TYPE_DANGER, implode("\n", $index->getFirstErrors()));
            Yii::error('Unable to index model ' . $event->model->className() . '::' . $event->model->getPrimaryKey() . ', error: ' . implode("\n", $index->getFirstErrors()));
        }
    }

    /**
     * @inheritdoc
     */
    public function deletePage($event)
    {
        $index = Index::find()->where(['model_id' => $event->model->getPrimaryKey(), 'model_class' => $event->model->className()])->one();
        ModuleEvent::trigger(self::EVENT_BEFORE_DELETE_INDEX, new ElasticIndexEvent([
            'model' => $event->model,
            'index' => $index,
            'sender' => $this,
        ]));

        if ($index) {
            $index->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_merge(parent::events(), [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
            SearchFormFrontend::EVENT_FETCH_ENDPOINTS => 'addSearchEndpointFrontend',
            SearchFormBackend::EVENT_FETCH_ENDPOINTS => 'addSearchEndpointBackend',
        ]);
    }
}