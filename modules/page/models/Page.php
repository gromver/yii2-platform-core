<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\models;


use dosamigos\transliterator\TransliteratorHelper;
use gromver\platform\core\behaviors\NestedSetsBehavior;
use gromver\platform\core\behaviors\SearchBehavior;
use gromver\platform\core\behaviors\TaggableBehavior;
use gromver\platform\core\behaviors\VersionBehavior;
use gromver\platform\core\components\UrlManager;
use gromver\platform\core\interfaces\model\SearchableInterface;
use gromver\platform\core\interfaces\model\ViewableInterface;
use gromver\platform\core\modules\user\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "grom_page".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $alias
 * @property string $path
 * @property string $preview_text
 * @property string $detail_text
 * @property string $metakey
 * @property string $metadesc
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property integer $ordering
 * @property integer $hits
 * @property integer $lock
 *
 * @property User $user
 * @property Page[] $translations
 * @property Page $parent
 * @property \gromver\platform\core\modules\tag\models\Tag[] $tags
 */
class Page extends \yii\db\ActiveRecord implements ViewableInterface, SearchableInterface
{
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'detail_text', 'status'], 'required'],
            [['preview_text', 'detail_text'], 'string'],
            [['parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['alias', 'metakey'], 'string', 'max' => 255],
            [['metadesc'], 'string', 'max' => 2048],

            [['alias'], 'filter', 'filter' => 'trim'],
            [['alias'], 'filter', 'filter' => function($value){
                    if (empty($value)) {
                        return Inflector::slug(TransliteratorHelper::process($this->title));
                    } else {
                        return Inflector::slug($value);
                    }
                }],
            [['alias'], 'unique', 'filter' => function($query){
                /** @var $query \yii\db\ActiveQuery */
                if($parent = self::findOne($this->parent_id)){
                    $query->andWhere('lft>=:lft AND rgt<=:rgt AND level=:level', [
                        'lft' => $parent->lft,
                        'rgt' => $parent->rgt,
                        'level' => $parent->level + 1,
                    ]);
                }
            }],
            [['alias'], 'string', 'max' => 250],
            [['alias'], 'required', 'enableClientValidation' => false],
            [['tags', 'versionNote', 'lock'], 'safe'],
            [['ordering'], 'filter', 'filter' => 'intVal'], //for proper $changedAttributes
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'parent_id' => Yii::t('gromver.platform', 'Parent'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'alias' => Yii::t('gromver.platform', 'Alias'),
            'path' => Yii::t('gromver.platform', 'Path'),
            'preview_text' => Yii::t('gromver.platform', 'Preview Text'),
            'detail_text' => Yii::t('gromver.platform', 'Detail Text'),
            'metakey' => Yii::t('gromver.platform', 'Meta keywords'),
            'metadesc' => Yii::t('gromver.platform', 'Meta description'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
            'updated_by' => Yii::t('gromver.platform', 'Updated By'),
            'lft' => Yii::t('gromver.platform', 'Lft'),
            'rgt' => Yii::t('gromver.platform', 'Rgt'),
            'level' => Yii::t('gromver.platform', 'Level'),
            'ordering' => Yii::t('gromver.platform', 'Ordering'),
            'hits' => Yii::t('gromver.platform', 'Hits'),
            'lock' => Yii::t('gromver.platform', 'Lock'),
            'tags' => Yii::t('gromver.platform', 'Tags'),
            'versionNote' => Yii::t('gromver.platform', 'Version Note')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            TaggableBehavior::className(),
            SearchBehavior::className(),
            NestedSetsBehavior::className(),
            [
                'class' => VersionBehavior::className(),//todo затестить чекаут в версию с уже занятым алиасом
                'attributes' => ['title', 'alias', 'preview_text', 'detail_text', 'metakey', 'metadesc']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     * @return PageQuery
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return PageQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    /**
     * @param bool $includeSelf
     * @return array
     */
    public function getBreadcrumbs($includeSelf = false)
    {
        if ($this->isRoot()) {
            return [];
        } else {
            $path = $this->parents()->excludeRoots()->all();
            if ($includeSelf) {
                $path[] = $this;
            }
            return array_map(function ($item) {
                /** @var self $item */
                return [
                    'label' => $item->title,
                    'url' => $item->getFrontendViewLink()
                ];
            }, $path);
        }
    }

    private static $_statuses = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
    ];

    /**
     * @return array
     */
    public static function statusLabels()
    {
        return array_map(function($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_statuses);
    }

    /**
     * @param string|null $status
     * @return string
     */
    public function getStatusLabel($status = null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }
        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    /**
     * @inheritdoc
     */
    public function optimisticLock()
    {
        return 'lock';
    }

    /**
     * Увеличивает счетчик просмотров
     * @return int
     */
    public function hit()
    {
        return 1;//return $this->updateAttributes(['hits' => $this->hits + 1]);
    }

    /**
     * @param bool $runValidation
     * @param null $attributes
     * @return bool
     */
    public function saveNode($runValidation = true, $attributes = null)
    {
        if ($this->getIsNewRecord()) {
            // если parent_id не задан, то ищем корневой элемент
            if($parent = $this->parent_id ? self::findOne($this->parent_id) : self::find()->roots()->one()) {
                $this->parent_id = $parent->id;
                return $this->appendTo($parent, $runValidation, $attributes);
            } else {
                // если рутового элемента не существует, то сохраняем модель как корневую
                return $this->makeRoot($runValidation, $attributes);
            }
        }

        // модель перемещена в другую модель
        if ($this->getOldAttribute('parent_id') != $this->parent_id && $newParent = $this->parent_id ? self::findOne($this->parent_id) : self::find()->roots()->one()) {
            $this->parent_id = $newParent->id;
            return $this->appendTo($newParent, $runValidation, $attributes);
        }
        // просто апдейт
        return $this->save($runValidation, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // нормализуем пути подэлементов для текущего элемента при его перемещении, либо изменении псевдонима
        if (array_key_exists('parent_id', $changedAttributes) || array_key_exists('alias', $changedAttributes)) {
            $this->refresh();
            $this->normalizePath();
        }

        // ранжируем элементы если нужно
        if (array_key_exists('ordering', $changedAttributes)) {
            $this->ordering ? $this->parent->reorderNode('ordering') : $this->parent->reorderNode('lft');
        }
    }

    /**
     * @return string
     */
    private function calculatePath()
    {
        $aliases = $this->parents()->excludeRoots()->select('alias')->column();
        return empty($aliases) ? $this->alias : implode('/', $aliases) . '/' . $this->alias;
    }

    /**
     * @param string $parentPath
     */
    public function normalizePath($parentPath = null)
    {
        if($parentPath === null) {
            $path = $this->calculatePath();
        } else {
            $path = $parentPath . '/' . $this->alias;
        }

        $this->updateAttributes(['path' => $path]);

        $children = $this->children(1)->all();
        foreach ($children as $child) {
            /** @var self $child */
            $child->normalizePath($path);
        }
    }

    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/page/frontend/default/view', 'id' => $this->id, 'alias' => $this->alias];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/page/frontend/default/view', 'id' => $model['id'], 'alias' => $model['alias']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/page/backend/default/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/page/backend/default/view', 'id' => $model['id']];
    }

    // SearchableInterface
    /**
     * @inheritdoc
     */
    public function getSearchTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchContent()
    {
        return $this->detail_text;
    }

    /**
     * @inheritdoc
     */
    public function getSearchTags()
    {
        return ArrayHelper::map($this->tags, 'id', 'title');
    }

    // SqlSearch integration
    /**
     * @param $event \gromver\platform\core\modules\search\modules\sql\widgets\events\SqlBeforeSearchEvent
     */
    static public function sqlBeforeFrontendSearch($event)
    {
        if ($event->modelClass == self::className()) {
            $event->query->leftJoin('{{%grom_page}}', [
                    'AND',
                    ['=', 'model_class', self::className()],
                    'model_id={{%grom_page}}.id',
                    ['NOT IN', '{{%grom_page}}.parent_id', Page::find()->unpublished()->select('{{%grom_page}}.id')->column()]
                ]
            )->addSelect('{{%grom_page}}.id')
                ->andWhere('model_class=:pageClassName XOR {{%grom_page}}.id IS NULL', [':pageClassName' => self::className()]);
        }
    }

    // ElasticSearch integration
    /**
     * @param $event \gromver\platform\core\modules\search\modules\elastic\widgets\events\ElasticBeforeSearchEvent
     */
    static public function elasticBeforeFrontendSearch($event)
    {
        if ($event->modelClass == self::className()) {
            $event->sender->filters[] = [
                'not' => [
                    'and' => [
                        [
                            'term' => ['model_class' => self::className()]
                        ],
                        [
                            'terms' => ['model_id' => self::find()->unpublished()->select('{{%grom_page}}.id')->column()]
                        ],
                    ]
                ]
            ];
        }
    }
}
