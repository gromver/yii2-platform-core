<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\user\models\User */
/* @var $user gromver\platform\core\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'Add User Param');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['backend/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'User: {name} (ID: {id})', ['name' => $user->username, 'id' => $user->id]), 'url' => ['backend/default/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Params'), 'url' => ['index', 'user_id' => $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
