<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\console;


use gromver\platform\core\modules\main\models\DbState;
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
 * @property string[] $adminEmail
 * @property array $supportEmail
 */
class Application extends \yii\console\Application
{
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
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $coreConfig = [];
        if (isset($config['basePath'])) {
            $coreConfig = @include($config['basePath'] . '/config/core/console.php');
            if (!is_array($coreConfig)) {
                $coreConfig = [];
            }
        }


        $config = ArrayHelper::merge([
            'controllerMap' => [
                'core-migrate' => 'gromver\platform\core\console\components\ModuleMigrateController'
            ],
            'components' => [
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                    'itemTable' => '{{%core_auth_item}}',
                    'itemChildTable' => '{{%core_auth_item_child}}',
                    'assignmentTable' => '{{%core_auth_assignment}}',
                    'ruleTable' => '{{%core_auth_rule}}'
                ],
                'cache' => ['class' => 'yii\caching\FileCache'],
                'elasticsearch' => ['class' => 'yii\elasticsearch\Connection'],
                'i18n' => [
                    'translations' => [
                        '*' => [
                            'class' => 'yii\i18n\PhpMessageSource'
                        ],
                    ],
                ],
                // фэйк для обхода BlamableBehavior
                'user' => 'gromver\platform\core\console\components\User',
                'paramsManager' => ['class' => 'gromver\platform\core\components\ParamsManager'],
            ],
            'modules' => [
                'main'      => ['class' => 'gromver\platform\core\modules\main\console\Module'],
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
            ]
        ], $coreConfig, $config);

        $this->_modulesHash = md5(json_encode($config['modules']));

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_modulesConfigDependency = new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()']);

        DbState::bootstrap();

        Yii::$container->set('gromver\modulequery\ModuleQuery', [
            'cache' => $this->cache,
            'cacheDependency' => new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()'])
        ]);
    }

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
     * @return string[]
     * @throws \yii\base\UnknownPropertyException
     */
    public function getAdminEmail()
    {
        return $this->paramsManager->params('main')->adminEmail;
    }

    /**
     * @return array ['support@example.com' => 'Support Name']
     * @throws \yii\base\UnknownPropertyException
     */
    public function getSupportEmail()
    {
        $email = $this->paramsManager->params('main')->supportEmail;

        return [
            $email['fromEmail'] => $email['fromName']
        ];
    }
}