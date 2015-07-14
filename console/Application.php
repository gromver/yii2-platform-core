<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\console;


use gromver\platform\core\traits\ApplicationLanguageTrait;
use yii\helpers\ArrayHelper;

/**
 * Class Application
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Application extends \yii\console\Application
{
    use ApplicationLanguageTrait;

    private $_modulesHash;

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $coreConfig = [];
        if (isset($config['basePath'])) {
            $coreConfig = @include($config['basePath'] . '/grom/console.php') or $coreConfig = [];
        }


        $config = ArrayHelper::merge([
            'controllerMap' => [
                'grom-migrate' => 'gromver\platform\core\console\components\ModuleMigrateController'
            ],
            /*'components' => [
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                    'itemTable' => '{{%grom_auth_item}}',
                    'itemChildTable' => '{{%grom_auth_item_child}}',
                    'assignmentTable' => '{{%grom_auth_assignment}}',
                    'ruleTable' => '{{%grom_auth_rule}}'
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
                'user' => 'gromver\platform\basic\console\components\User'
            ],
            'modules' => [
                'grom' => [
                    'class' => 'gromver\platform\basic\modules\main\console\Module',
                    'modules' => [
                        'user'      => ['class' => 'gromver\platform\basic\modules\user\Module'],
                        'auth'      => ['class' => 'gromver\platform\basic\modules\auth\Module'],
                        'menu'      => ['class' => 'gromver\platform\basic\modules\menu\Module'],
                        'news'      => ['class' => 'gromver\platform\basic\modules\news\Module'],
                        'page'      => ['class' => 'gromver\platform\basic\modules\page\Module'],
                        'tag'       => ['class' => 'gromver\platform\basic\modules\tag\Module'],
                        'version'   => ['class' => 'gromver\platform\basic\modules\version\Module'],
                        'widget'    => ['class' => 'gromver\platform\basic\modules\widget\Module'],
                        'media'     => ['class' => 'gromver\platform\basic\modules\media\Module'],
                        'search'    => [
                            'class' => 'gromver\platform\basic\modules\search\Module',
                            'modules' => [
                                'sql' => ['class' => 'gromver\platform\basic\modules\search\modules\sql\Module']
                            ]
                        ],
                    ]
                ]
            ]*/
        ], $coreConfig, $config);

        $this->_modulesHash = md5(json_encode($config['modules']));

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    /*public function init()
    {
        $this->bootstrap = array_merge($this->bootstrap, ['grom']);

        parent::init();
    }*/

    /**
     * @return string
     */
    public function getModulesHash() {
        return $this->_modulesHash;
    }
}