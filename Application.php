<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core;


use gromver\platform\core\components\MenuManager;
use gromver\platform\core\modules\main\models\DbState;
use gromver\platform\core\modules\menu\models\MenuItem;
use yii\base\Event;
use yii\caching\ExpressionDependency;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class Application
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\components\ParamsManager $paramsManager
 * @property string $siteName
 * @property string $siteTitle
 * @property string $siteSlogan
 */
class Application extends \yii\web\Application {
    const SESSION_MODE_KEY = '__grom_mode';

    const MODE_EDIT = 'edit';
    const MODE_VIEW = 'view';

    const EVENT_FETCH_LIST_ITEMS = 'mainFetchListItems';

    public $defaultRoute = 'main/frontend/default/index';

    public $layout          = '@gromver/platform/core/views/layouts/frontend';

    public $layoutFrontend  = '@gromver/platform/core/views/layouts/frontend';
    public $layoutBackend   = '@gromver/platform/core/views/layouts/backend';
    public $layoutError     = '@gromver/platform/core/views/layouts/error';
    public $layoutModal     = '@gromver/platform/core/views/layouts/modal';

    /**
     * Здесь можно указать дополнительные опции для списков, см. \gromver\models\fields\ListField
     * 'foo\bar\SomeClass::listAttribute' => [
     *      ['value1' => 'Доп опция 1'],
     *      [value => text],
     *      ...
     * ]
     * @var array
     */
    public $listFieldItems = [];
    /**
     * @var array список компонентов к которым нельзя попасть на прямую(grom/post/frontend/..., grom/page/frontend/...)
     * эта блокировка нужна для того чтобы управлять структурой сайта только через меню
     * во время разработки проекта ету блокировку можно снять указав в конфиге приложения
     * [
     *      'blockedUrlRules' => []
     * ]
     */
    public $blockingUrlRules = [
        /*'grom/news/frontend<path:(/.*)?>',
        'grom/page/frontend<path:(/.*)?>',
        'grom/tag/frontend<path:(/.*)?>',
        'grom/user/frontend<path:(/.*)?>',*/
    ];
    /**
     * @var array список дополнительных поведений для модели пользователя
     * Используется в \gromver\platform\core\modules\user\models\User::behaviors()
     */
    public $userBehaviors = [];

    /**
     * @var string
     */
    private $_modulesHash;
    /**
     * @var null|\yii\caching\Dependency
     */
    private $_modulesConfigDependency;
    /**
     * @var string
     */
    private $_mode;

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $coreConfig = [];
        if (isset($config['basePath'])) {
            $coreConfig = @include($config['basePath'] . '/grom/web.php') or $coreConfig = [];
        }

        $config = ArrayHelper::merge([
            'components' => [
                /*'request' => [
                    'class' => 'gromver\platform\core\components\Request',
                ],*/
                'urlManager' => [
                    //'class' => 'gromver\platform\core\components\UrlManager',
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                ],
                'user' => [
                    'class' => 'gromver\platform\core\components\User',
                ],
                'errorHandler' => [
                    'class' => 'yii\web\ErrorHandler',
                    'errorAction' => '/main/common/default/error'
                ],
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                    'itemTable' => '{{%grom_auth_item}}',
                    'itemChildTable' => '{{%grom_auth_item_child}}',
                    'assignmentTable' => '{{%grom_auth_assignment}}',
                    'ruleTable' => '{{%grom_auth_rule}}'
                ],
                'cache' => ['class' => 'yii\caching\FileCache'],
                'elasticsearch' => ['class' => 'yii\elasticsearch\Connection'],
                'assetManager' => [
                    'bundles' => [
                        'mihaildev\ckeditor\Assets' => [
                            'sourcePath' => '@gromver/platform/core/assets/ckeditor',
                        ],
                    ],
                ],
                'i18n' => [
                    'translations' => [
                        '*' => [
                            'class' => 'yii\i18n\PhpMessageSource'
                        ],
                        'gromver.*' => [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'basePath' => '@gromver/platform/core/messages',
                        ]
                    ],
                ],
                'paramsManager' => ['class' => 'gromver\platform\core\components\ParamsManager'],
            ],
            'modules' => [
                'main'      => ['class' => 'gromver\platform\core\modules\main\Module'],
                'user'      => ['class' => 'gromver\platform\core\modules\user\Module'],
                'auth'      => ['class' => 'gromver\platform\core\modules\auth\Module'],
                'menu'      => ['class' => 'gromver\platform\core\modules\menu\Module'],
                'widget'    => ['class' => 'gromver\platform\core\modules\widget\Module'],
                'media'     => ['class' => 'gromver\platform\core\modules\media\Module'],
                'page'      => ['class' => 'gromver\platform\core\modules\page\Module'],
                'tag'       => ['class' => 'gromver\platform\core\modules\tag\Module'],
                'version'   => ['class' => 'gromver\platform\core\modules\version\Module'],
                'search'    => [
                    'class' => 'gromver\platform\core\modules\search\Module',
                    'modules' => [
                        'sql' => ['class' => 'gromver\platform\core\modules\search\modules\sql\Module']
                    ]
                ],
                'gridview' => ['class' => 'kartik\grid\Module']
            ]
        ], $coreConfig, $config);

        $this->_modulesHash = md5(json_encode(ArrayHelper::getValue($config, 'modules', [])));

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        //$this->initI18N();

        //$this->bootstrap = array_merge($this->bootstrap, ['main']);

        parent::init();

        $this->_modulesConfigDependency = new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()']);

        DbState::bootstrap();

        Yii::$container->set('gromver\models\fields\EditorField', [
            'controller' => 'media/manager',
            'editorOptions' => [
                'filebrowserBrowseUrl' => ['/menu/backend/item/ckeditor-select'],
                //'extraPlugins' => 'codesnippet',
                'autoGrow_onStartup' => true,
                'autoGrow_bottomSpace' => 50,
            ]
        ]);
        Yii::$container->set('gromver\models\fields\MediaField', [
            'controller' => 'media/manager'
        ]);
        Yii::$container->set('gromver\modulequery\ModuleQuery', [
            'cache' => $this->cache,
            'cacheDependency' => $this->_modulesConfigDependency
        ]);
        Yii::$container->set('gromver\platform\core\components\MenuMap', [
            'cache' => $this->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName())
        ]);
        Yii::$container->set('gromver\platform\core\components\MenuUrlRule', [
            'cache' => $this->cache,
            'cacheDependency' => $this->_modulesConfigDependency
        ]);
        Yii::$container->set('gromver\platform\core\modules\main\widgets\Desktop', [
            'cache' => $this->cache,
            'cacheDependency' => $this->_modulesConfigDependency
        ]);
        Yii::$container->set('gromver\platform\core\components\ParamsManager', [
            'cache' => $this->cache,
            'cacheDependency' => $this->_modulesConfigDependency
        ]);

        /** @var MenuManager $manager */
        $rules['auth'] = 'auth/default/login';
        $rules['admin'] = 'main/backend/default/index';
        if (is_array($this->blockingUrlRules) && count($this->blockingUrlRules)) {
            foreach ($this->blockingUrlRules as $rule) {
                $rules[$rule] = 'main/default/page-not-found'; //блокируем доступ напрямую
            }
        }

        $this->urlManager->addRules($rules, false); //вставляем в начало списка

        $this->set('menuManager', \Yii::createObject(MenuManager::className()));

        // пропускаем \gromver\models\fields\events\ListItemsEvent событие, через ModuleEvent - не факт, что нужно, но почему бы и нет
        Event::on('\gromver\models\fields\ListField', 'fetchItems', function($event) {
            /** @var $event \gromver\models\fields\events\ListItemsEvent */
            $additionalItems = @$this->listFieldItems[$event->model->getSourceClass().'::'.$event->attribute];
            if (is_array($additionalItems)) {
                $event->items = array_merge($event->items, $additionalItems);
            }
        });

        $this->applyDefaultMetadata();

        //parent::init();
    }

    /*public function initI18N()
    {
        $this->i18n->translations['gromver.*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@gromver/platform/core/messages',
        ];
    }*/

    /**
     * @return string
     */
    public function getModulesHash() {
        return $this->_modulesHash;
    }

    /**
     * @return null|\yii\caching\Dependency
     */
    public function getModulesConfigDependency()
    {
        return $this->_modulesConfigDependency;
    }

    /**
     * @param string $mode
     * @param bool $saveInSession
     */
    public function setMode($mode, $saveInSession = true)
    {
        $this->_mode = in_array($mode, self::modes()) ? $mode : self::MODE_VIEW;

        if ($saveInSession) {
            Yii::$app->session->set(self::SESSION_MODE_KEY, $mode);
        }
    }

    /**
     * @return string
     */
    public function getMode()
    {
        if(!isset($this->_mode)) {
            $this->setMode(Yii::$app->session->get(self::SESSION_MODE_KEY, self::MODE_VIEW));
        }

        return $this->_mode;
    }

    /**
     * @return bool
     */
    public function getIsEditMode()
    {
        return $this->getMode() === self::MODE_EDIT;
    }

    /**
     * @return array
     */
    public static function modes()
    {
        return [self::MODE_VIEW, self::MODE_EDIT];
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        /** @var \gromver\platform\core\modules\main\models\MainParams $params */
        $params = $this->paramsManager->params('main');

        return !empty($params->siteName) ? $params->siteName : $this->name;
    }

    /**
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->paramsManager->params('main')->siteTitle;
    }

    /**
     * @return string
     */
    public function getSiteSlogan()
    {
        return $this->paramsManager->params('main')->siteSlogan;
    }

    /**
     * @return \yii\elasticsearch\Connection
     */
    public function getElasticSearch()
    {
        return $this->get('elasticsearch');
    }

    /**
     * apply platform's frontend layout
     */
    public function applyDefaultMetadata()
    {
        /** @var \gromver\platform\core\modules\main\models\MainParams $params */
        $params = $this->paramsManager->params('main');

        // устанавливает мета описание сайта по умолчанию
        $view = $this->getView();
        $view->title = $params->siteTitle ? $params->siteTitle : $params->siteName;
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
     * apply platform's frontend layout
     */
    public function applyFrontendLayout()
    {
        Yii::$app->layout = $this->layoutFrontend;
    }

    /**
     * apply platform's backend layout
     */
    public function applyBackendLayout()
    {
        Yii::$app->layout = $this->layoutBackend;
    }

    /**
     * apply platform's error layout
     */
    public function applyErrorLayout()
    {
        Yii::$app->layout = $this->layoutError;
    }

    /**
     * apply platform's modal layout
     */
    public function applyModalLayout()
    {
        Yii::$app->layout = $this->layoutModal;
    }
}