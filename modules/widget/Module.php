<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\widget;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\modules\main\widgets\Desktop;
use Yii;

/**
 * Class Module
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\core\modules\widget\controllers';
    public $defaultRoute = 'backend/default';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Widgets'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Widget\'s Settings'), 'url' => ['/widget/backend/default/index']],
                ['label' => Yii::t('gromver.platform', 'Widget\'s Personal Settings'), 'url' => ['/widget/backend/personal/index']]
            ]
        ];
    }
}
