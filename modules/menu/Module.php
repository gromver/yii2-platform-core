<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\menu;


use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\modules\menu\events\MenuItemLayoutsModuleEvent;
use gromver\platform\core\modules\main\widgets\Desktop;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    const EVENT_MENU_ITEM_LAYOUTS = 'menuItemLayouts';

    public $controllerNamespace = 'gromver\platform\core\modules\menu\controllers';
    public $defaultRoute = 'backend/item';

    private $_menuItemLayouts = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem'
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Menu'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['/menu/backend/type/index']],
                ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['/menu/backend/item/index']],
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMenuItemLayouts()
    {
        return ModuleEvent::trigger(self::EVENT_MENU_ITEM_LAYOUTS, new MenuItemLayoutsModuleEvent(['items' => $this->_menuItemLayouts]), 'items');
    }

    /**
     * @param array $items
     */
    public function setMenuItemLayouts($items)
    {
        $this->_menuItemLayouts = $items;
    }
}
