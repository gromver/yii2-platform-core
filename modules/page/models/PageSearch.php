<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class PageSearch represents the model behind the search form about `gromver\platform\core\modules\page\models\Page`.
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageSearch extends Page
{
    /**
     * @var integer[]
     */
    public $tags;
    /**
     * @var bool
     */
    public $excludeRoots = true;
    /**
     * @var integer
     */
    public $excludePage;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock', 'excludeRoots', 'excludePage'], 'integer'],
            [['title', 'alias', 'path', 'preview_text', 'detail_text', 'metakey', 'metadesc', 'tags', 'versionNote'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Page::find();
        $query->with(['tags', 'parent']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            if ($this->excludeRoots) {
                $query->excludeRoots();
            }

            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%core_page}}.id' => $this->id,
            '{{%core_page}}.parent_id' => $this->parent_id,
            '{{%core_page}}.created_at' => $this->created_at,
            '{{%core_page}}.updated_at' => $this->updated_at,
            '{{%core_page}}.status' => $this->status,
            '{{%core_page}}.created_by' => $this->created_by,
            '{{%core_page}}.updated_by' => $this->updated_by,
            '{{%core_page}}.lft' => $this->lft,
            '{{%core_page}}.rgt' => $this->rgt,
            '{{%core_page}}.level' => $this->level,
            '{{%core_page}}.ordering' => $this->ordering,
            '{{%core_page}}.hits' => $this->hits,
            '{{%core_page}}.lock' => $this->lock,
        ]);

        $query->andFilterWhere(['like', '{{%core_page}}.title', $this->title])
            ->andFilterWhere(['like', '{{%core_page}}.path', $this->path])
            ->andFilterWhere(['like', '{{%core_page}}.alias', $this->alias])
            ->andFilterWhere(['like', '{{%core_page}}.preview_text', $this->preview_text])
            ->andFilterWhere(['like', '{{%core_page}}.detail_text', $this->detail_text])
            ->andFilterWhere(['like', '{{%core_page}}.metakey', $this->metakey])
            ->andFilterWhere(['like', '{{%core_page}}.metadesc', $this->metadesc]);

        if ($this->excludeRoots) {
            $query->excludeRoots();
        }

        if ($this->excludePage && $page = Page::findOne($this->excludePage)) {
            /** @var $page Page */
            $query->excludePage($page);
        }

        if($this->tags) {
            $query->innerJoinWith('tags')->andFilterWhere(['{{%core_tag}}.id' => $this->tags]);
        }

        return $dataProvider;
    }
}
