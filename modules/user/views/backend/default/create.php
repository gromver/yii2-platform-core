<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\user\models\User */

$this->title = Yii::t('gromver.platform', 'Add User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
