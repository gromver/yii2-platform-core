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

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
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
