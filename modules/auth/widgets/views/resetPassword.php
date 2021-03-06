<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var gromver\platform\core\modules\auth\models\ForgotPasswordForm $model
 * @var string|array|null $url
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'reset-password-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'password', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'autocomplete' => 'off']) ?>
<?= $form->field($model, 'passwordConfirm', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password_confirm')]) ?>

<div class="form-group">
    <?= Html::submitButton(\Yii::t('gromver.platform', 'Submit'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
</div>

<?php ActiveForm::end(); ?>