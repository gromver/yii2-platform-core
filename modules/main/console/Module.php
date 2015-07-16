<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main\console;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\components\ParamsManager;
use gromver\platform\core\modules\main\models\MainParams;
use Yii;
use yii\base\BootstrapInterface;
use yii\caching\ExpressionDependency;

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
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param \yii\web\Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->set('grom', $this);

        Yii::$container->set('gromver\modulequery\ModuleQuery', [
            'cache' => $app->cache,
            'cacheDependency' => new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()'])
        ]);
    }

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
}
