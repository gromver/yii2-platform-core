<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\components;


use gromver\platform\core\modules\menu\models\MenuItem;
use Yii;

/**
 * Class MenuManager
 * Компонент меню, доступен через Yii::$app->menuManager
 * Данный компонент инициирует маршрутизацию меню, через MenuUrlRule, а также дает доступ к активному пнукту меню
 * и картам пуктов меню [[MenuManager::getMenuMap]]
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property MenuItem $activeMenu
 * @property integer[] $activeMenuIds
 */
class MenuManager extends \yii\base\Object
{
    /**
     * @var MenuItem
     */
    private $_activeMenu;
    /**
     * @var integer[]
     */
    private $_activeMenuIds = [];
    /**
     * @var MenuMap
     */
    private $_map;

    public function init()
    {
        Yii::$app->urlManager->addRules([Yii::createObject([
            'class' => MenuUrlRule::className(),
            'menuManager' => $this
        ])], false); //вставляем в начало списка
    }

    /**
     * @return MenuMap
     */
    public function getMenuMap()
    {
        if (!isset($this->_map)) {
            $this->_map = Yii::createObject(MenuMap::className());
        }

        return $this->_map;
    }

    public function setActiveMenu($value)
    {
        $this->_activeMenu = $value;
    }

    public function getActiveMenu()
    {
        return $this->_activeMenu;
    }

    public function addActiveMenuId($value)
    {
        $this->_activeMenuIds[] = $value;
    }

    public function setActiveMenuIds($value)
    {
        $this->_activeMenuIds = $value;
    }

    public function getActiveMenuIds()
    {
        return $this->_activeMenuIds;
    }
}