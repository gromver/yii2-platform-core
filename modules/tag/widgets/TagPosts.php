<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\tag\widgets;


use gromver\platform\core\modules\news\models\Post;
use gromver\platform\core\modules\tag\widgets\assets\TagAsset;
use gromver\platform\core\modules\tag\models\Tag;
use gromver\platform\core\modules\widget\widgets\Widget;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * Class TagPosts
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TagPosts extends Widget
{
    /**
     * Tag or TagId or TagId:TagAlias
     * @var Tag|string
     * @field modal
     * @url /grom/tag/backend/default/select
     * @translation gromver.platform
     */
    public $tag;
    /**
     * CategoryId
     * @var string
     * @field modal
     * @url /grom/news/backend/category/select
     * @translation gromver.platform
     */
    public $categoryId;
    /**
     * @field list
     * @items layouts
     * @editable
     * @translation gromver.platform
     */
    public $layout = 'tag/postsDefault';
    /**
     * @field list
     * @items itemLayouts
     * @editable
     * @translation gromver.platform
     */
    public $itemLayout = '_itemPost';
    /**
     * @translation gromver.platform
     */
    public $pageSize = 20;
    /**
     * @field list
     * @editable
     * @items sortColumns
     * @var string
     * @translation gromver.platform
     */
    public $sort = 'published_at';
    /**
     * @field list
     * @editable
     * @items sortDirections
     * @translation gromver.platform
     */
    public $dir = SORT_DESC;
    /**
     * @ignore
     */
    public $listViewOptions = [];

    protected function launch()
    {
        if (!$this->tag instanceof Tag) {
            $this->tag = Tag::findOne(intval($this->tag));
        }

        if (empty($this->tag)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Tag must be set.'));
        }

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $this->getQuery(),
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ],
                    'sort' => [
                        'defaultOrder' => [$this->sort => (int)$this->dir]
                    ]
                ]),
            'itemLayout' => $this->itemLayout,
            'model' => $this->tag
        ]);

        $this->getView()->registerAssetBundle(TagAsset::className());
    }

    protected function getQuery()
    {
        return Post::find()->published()->category($this->categoryId)->innerJoinWith('tags', false)->andWhere(['{{%grom_tag}}.id' => $this->tag->id]);
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/grom/tag/backend/default/update', 'id' => $this->tag->id, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Update Tag')]
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'tag/postsDefault' => Yii::t('gromver.platform', 'Default'),
            'tag/postsList' => Yii::t('gromver.platform', 'List'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemPost' => Yii::t('gromver.platform', 'Default'),
        ];
    }


    public static function sortColumns()
    {
        return [
            'published_at' => Yii::t('gromver.platform', 'By publish date'),
            'created_at' => Yii::t('gromver.platform', 'By create date'),
            'title' => Yii::t('gromver.platform', 'By name'),
            'ordering' => Yii::t('gromver.platform', 'By order'),
        ];
    }

    public static function sortDirections()
    {
        return [
            SORT_ASC => Yii::t('gromver.platform', 'Asc'),
            SORT_DESC => Yii::t('gromver.platform', 'Desc'),
        ];
    }
} 