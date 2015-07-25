<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user gromver\platform\core\modules\user\models\User */
/* @var $params gromver\platform\core\modules\user\models\UserParam[] */

$this->title = Yii::t('gromver.platform', 'User Params: {name} (ID: {id})', [
    'id' => $user->id,
    'name' => $user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', $user->getIsTrashed() ? 'Trash' : 'Users'), 'url' => [$user->getIsTrashed() ? 'index-trash' : 'index']];
$this->params['breadcrumbs'][] = ['label' => $user->username . " (ID: $user->id)", 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('gromver.platform', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered detail-view">
        <tbody>
        <?php foreach ($params as $param) { ?>
            <tr>
                <th><?= $param->name ?></th>
                <td><?= $param->value ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
