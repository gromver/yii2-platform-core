<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user gromver\platform\core\modules\user\models\User */
/* @var $params gromver\platform\core\modules\user\models\UserParam[] */

$this->title = Yii::t('gromver.platform', 'Params');
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Account'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
