<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\page\models\Page */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['index']];
foreach ($model->parents()->excludeRoots()->all() as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->title, 'url' => ['index', 'PageSearch' => ['parent_id' => $parent->id]]];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'parentId' => $model->parent_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('gromver.platform', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'parent_id',
            'title',
            'alias',
            'path',
            'preview_text:ntext',
            'detail_text:ntext',
            'metakey',
            'metadesc',
            'created_at:datetime',
            'updated_at:datetime',
            'status',
            'created_by',
            'updated_by',
            'lft',
            'rgt',
            'level',
            'ordering',
            'hits',
            'lock',
            [
                'attribute' => 'tags',
                'value' => implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', function($tag) {
                    /** @var $tag \gromver\platform\core\modules\tag\models\Tag */
                    return Html::a($tag->title, $tag->getBackendViewLink());
                })),
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
