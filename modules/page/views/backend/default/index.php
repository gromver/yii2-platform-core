<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\core\modules\page\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'Page',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>*/?>

    <?= GridView::widget([
        'id' => 'table-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'columns' => [
            ['class' => '\kartik\grid\CheckboxColumn'],
            [
                'attribute' => 'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'parent_id',
                'width' => '150px',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var \gromver\platform\core\modules\page\models\Page $model */
                    return $model->parent ? $model->parent->title : '';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\page\models\Page::find()->where(['id' => $searchModel->parent_id])->all(), 'id', 'title'),
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['page-list']),
                        ],
                    ],
                ]
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var \gromver\platform\core\modules\page\models\Page $model */
                    return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
                },
                'format' => 'html'
            ],
            //'alias',
            [
                'attribute' => 'status',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                        /** @var $model \gromver\platform\core\modules\page\models\Page */
                        return $model->status === \gromver\platform\core\modules\page\models\Page::STATUS_PUBLISHED ? Html::a('<i class="glyphicon glyphicon-ok-circle"></i>', \yii\helpers\Url::to(['unpublish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']) : Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', \yii\helpers\Url::to(['publish', 'id' => $model->id]), ['class' => 'btn btn-danger btn-xs', 'data-pjax' => '0', 'data-method' => 'post']);
                    },
                'filter' => \gromver\platform\core\modules\page\models\Page::statusLabels(),
                'format' => 'raw',
                'width' => '100px'
            ],
            [
                'attribute' => 'tags',
                'width' => '120px',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var $model \gromver\platform\core\modules\page\models\Page */
                    return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title'),
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/tag/backend/default/tag-list']),
                        ],
                    ],
                ]
            ],
            [
                'attribute' => 'ordering',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model, $index) {
                    /** @var \gromver\platform\core\modules\page\models\Page $model */
                    return Html::input('text', 'order', $model->ordering, ['class'=>'form-control']);
                },
                'format' => 'raw',
                'width' => '100px'
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'deleteOptions' => ['data-method' => 'delete']
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . '</h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), $searchModel->parent_id ? ['create', 'parentId' => $searchModel->parent_id] : ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-sort-by-attributes"></i> ' . Yii::t('gromver.platform', 'Ordering'), ['ordering'], ['class' => 'btn btn-default', 'data-pjax' => '0', 'onclick' => 'processOrdering(this); return false']).' '.
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax' => '0', 'onclick' => 'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]) ?>

</div>
<script>
    function processOrdering(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows'),
            data = {}
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }
        $.each(selection, function(index, value){
            data[value] = $grid.find('tr[data-key="'+value+'"] input[name="order"]').val()
        })

        $.post($el.attr('href'), {data:data}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
    function processAction(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows')
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>