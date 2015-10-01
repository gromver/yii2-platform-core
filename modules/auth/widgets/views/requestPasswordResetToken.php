<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\core\modules\auth\models\ForgotPasswordForm $model
 * @var string|array|null $url
 * @var string|array|null $captchaAction
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'request-password-reset-token-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'email', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-envelope"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

<?php if ($model->scenario == $model::SCENARIO_REQUEST_WITH_CAPTCHA) {
    echo $form->field($model, 'verifyCode', ['options' => ['class' => 'form-group input-group input-group-lg captcha-group'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>{input}'])->widget(Captcha::className(), ['captchaAction' => $captchaAction, 'options' => ['placeholder' => $model->getAttributeLabel('verifyCode'), 'class' => 'form-control']]);
} ?>

<div class="form-group">
    <?= Html::submitButton(\Yii::t('gromver.platform', 'Submit'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
</div>

<?php ActiveForm::end(); ?>