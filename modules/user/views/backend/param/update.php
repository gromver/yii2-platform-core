<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\user\models\UserParam */
/* @var $user gromver\platform\core\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'Update', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['backend/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'User: {name} (ID: {id})', ['name' => $user->username, 'id' => $user->id]), 'url' => ['backend/default/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Params'), 'url' => ['index', 'user_id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'user_id' => $user->id, 'name' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
