<?php

use yii\helpers\Html;

/**
 * @var gromver\models\ObjectModel $model
 * @var array $paramsMenuItems
 * @var yii\web\View $this
 */
$this->title = Yii::t('gromver.platform', 'System Configuration');
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="page-heading">
    <h2><?= \yii\helpers\Html::encode($this->title) ?></h2>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= \kartik\sidenav\SideNav::widget([
            'items' => $paramsMenuItems
        ]) ?>
    </div>
    <div class="col-sm-9">
        <div class="config-form">

            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'layout' => 'horizontal',
                'options' => [
                    'class' => 'form-params col-xs-12'
                ],
            ]); ?>

            <?= \gromver\models\widgets\Fields::widget(['model' => $model]) ?>

            <div class="row">
                <?= Html::submitButton('<i class="glyphicon glyphicon-save"></i> ' . Yii::t('gromver.platform', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php \yii\bootstrap\ActiveForm::end(); ?>

        </div>
    </div>
</div>

<?php /*$this->registerJs('$("#'.$form->getId().'").on("refresh.form", function(){
    $(this).find("button[value=\'refresh\']").click()
})'); */?>