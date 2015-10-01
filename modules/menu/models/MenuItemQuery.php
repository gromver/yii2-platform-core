<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\menu\models;


use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class MenuItemQuery
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuItemQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    /**
     * @param $typeId
     * @return static
     */
    public function type($typeId)
    {
        return $this->andWhere(['{{%core_menu_item}}.menu_type_id' => $typeId]);
    }
    /**
     * @return static
     */
    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%core_menu_item}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%core_menu_item}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status = ' . MenuItem::STATUS_UNPUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%core_menu_item}}.id', $badcatsQuery]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%core_menu_item}}.lft!=1');
    }

    /**
     * Исключает из выборки пункт меню $item и все его подпункты
     * @param MenuItem $item
     * @return static
     */
    public function excludeItem($item)
    {
        return $this->andWhere('{{%core_menu_item}}.lft < :excludeLft OR {{%core_menu_item}}.lft > :excludeRgt', [':excludeLft' => $item->lft, ':excludeRgt' => $item->rgt]);
    }
}