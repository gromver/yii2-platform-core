<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\tag\widgets;


use gromver\platform\news\models\Post;
use gromver\platform\core\modules\tag\widgets\assets\TagAsset;
use gromver\platform\core\modules\widget\widgets\Widget;

/**
 * Class TagItems
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TagPostCloud extends Widget
{
    /**
     * @label Category
     * @translation gromver.platform
     */
    public $categoryId;
    /**
     * @translation gromver.platform
     */
    public $fontBase = 14;
    /**
     * @translation gromver.platform
     */
    public $fontSpace = 6;

    protected function launch()
    {
        $tags = Post::find()->category($this->categoryId)->published()->innerJoinWith('tags', false)->select([
            'id' => '{{%core_tag}}.id',
            'title' => '{{%core_tag}}.title',
            'alias' => '{{%core_tag}}.alias',
            'weight' => 'count({{%core_tag}}.id)'
        ])->groupBy('{{%core_tag}}.id')->asArray()->all();

        $maxWeight = 0;
        array_walk($tags, function ($v) use (&$maxWeight){
            $maxWeight = max($v['weight'], $maxWeight);
        });

        echo $this->render('tag/tagPostCloud', [
            'tags' => $tags,
            'fontBase' => $this->fontBase,
            'fontSpace' => $this->fontSpace,
            'maxWeight' => $maxWeight,
            'categoryId' => $this->categoryId
        ]);

        $this->getView()->registerAssetBundle(TagAsset::className());
    }
} 