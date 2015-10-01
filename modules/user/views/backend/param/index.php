<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\core\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user gromver\platform\core\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'Params');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['backend/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'User: {name} (ID: {id})', ['name' => $user->username, 'id' => $user->id]), 'url' => ['backend/default/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php /*// echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('gromver.platform', 'Create {modelClass}', [
    'modelClass' => 'User',
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
            [
                'class' => '\kartik\grid\CheckboxColumn',
//                'checkboxOptions' => function ($model, $key, $index, $column) {
//                    /** @var gromver\platform\core\modules\user\models\UserParam $model */
//                    return ['value' => $model->name];
//                }
            ],
//            [
//                'attribute' => 'id',
//                'hAlign' => GridView::ALIGN_CENTER,
//                'vAlign' => GridView::ALIGN_MIDDLE,
//                'width' => '60px'
//            ],
            [
                'attribute' => 'name',
                'vAlign' => GridView::ALIGN_MIDDLE,
            ],
            [
                'attribute' => 'value',
                'vAlign' => GridView::ALIGN_MIDDLE,
            ],
            [
                'attribute' => 'created_at',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'format' => 'datetime',
                'width' => '180px',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'width' => '100px',
                'deleteOptions' => ['data-method' => 'delete'],
//                'template' => '{login} {params} {view} {update} {trash}',
//                'buttons' => [
//                    'params' => function ($url, $model, $key) {
//                            /** @var User $model */
//                            return Html::a('<i class="glyphicon glyphicon-user"></i>', ['profile', 'id' => $model->id], ['title' => Yii::t('gromver.platform', 'User Profile'), 'data-pjax' => 0]);
//                        },
//                ]
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'user_id' => $user->id], ['class' => 'btn btn-success', 'data-pjax' => 0]),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete', 'user_id' => $user->id], ['class' => 'btn btn-danger', 'data-pjax'=>'0', 'onclick'=>'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index', 'user_id' => $user->id], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]) ?>

</div>
<script>
    function processAction(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows')
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Выберите элементы.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>