<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\models;


use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class PageQuery
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            [
                'class' => NestedSetsQueryBehavior::className(),
            ],
        ];
    }
    /**
     * @return static
     */
    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%grom_page}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%grom_page}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status != ' . Page::STATUS_PUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%grom_page}}.id', $badcatsQuery]);
    }

    /**
     * @return static
     */
    public function unpublished()
    {
        return $this->innerJoin('{{%grom_page}} AS ancestors', '{{%grom_page}}.lft >= ancestors.lft AND {{%grom_page}}.rgt <= ancestors.rgt')->andWhere('ancestors.status != ' . Page::STATUS_PUBLISHED)->addGroupBy(['{{%grom_page}}.id']);
    }

    /**
     * Фильтр по категории
     * @param integer $id
     * @return $this
     */
    public function parent($id)
    {
        return $this->andWhere(['{{%grom_page}}.parent_id' => $id]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%grom_page}}.lft!=1');
    }

    /**
     * Исключает из выборки страницу $page и все ее подстраницы
     * @param Page $page
     * @return static
     */
    public function excludePage($page)
    {
        return $this->andWhere('{{%grom_page}}.lft < :excludeLft OR {{%grom_page}}.lft > :excludeRgt', [':excludeLft' => $page->lft, ':excludeRgt' => $page->rgt]);
    }
} 