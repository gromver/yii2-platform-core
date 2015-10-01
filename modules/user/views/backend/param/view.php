<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\user\models\UserParam */
/* @var $user gromver\platform\core\modules\user\models\User */

$this->title = Yii::t('gromver.platform', '{name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['backend/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'User: {name} (ID: {id})', ['name' => $user->username, 'id' => $user->id]), 'url' => ['backend/default/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Params'), 'url' => ['index', 'user_id' => $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'user_id' => $user->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'user_id' => $user->id, 'name' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'user_id' => $user->id, 'name' => $model->name], [
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
            'user_id',
            'name',
            'value',
            'created_at:datetime',
        ],
    ]) ?>

</div>
