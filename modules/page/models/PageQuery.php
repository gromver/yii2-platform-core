<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\models;


use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class PageQuery
 * @package yii2-platform-core
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
            'from' => ['{{%core_page}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%core_page}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status != ' . Page::STATUS_PUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%core_page}}.id', $badcatsQuery]);
    }

    /**
     * @return static
     */
    public function unpublished()
    {
        return $this->innerJoin('{{%core_page}} AS ancestors', '{{%core_page}}.lft >= ancestors.lft AND {{%core_page}}.rgt <= ancestors.rgt')->andWhere('ancestors.status != ' . Page::STATUS_PUBLISHED)->addGroupBy(['{{%core_page}}.id']);
    }

    /**
     * Фильтр по категории
     * @param integer $id
     * @return $this
     */
    public function parent($id)
    {
        return $this->andWhere(['{{%core_page}}.parent_id' => $id]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%core_page}}.lft!=1');
    }

    /**
     * Исключает из выборки страницу $page и все ее подстраницы
     * @param Page $page
     * @return static
     */
    public function excludePage($page)
    {
        return $this->andWhere('{{%core_page}}.lft < :excludeLft OR {{%core_page}}.lft > :excludeRgt', [':excludeLft' => $page->lft, ':excludeRgt' => $page->rgt]);
    }
} 