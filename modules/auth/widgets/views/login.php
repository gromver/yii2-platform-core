<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\core\modules\user\models\User $model
 * @var string|array|null $url
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

\gromver\platform\core\modules\auth\widgets\assets\AuthAsset::register($this);
?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<?//= $form->errorSummary($model) ?>

<?= $form->field($model, 'username', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-user"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

<?= $form->field($model, 'password', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

<?php if ($model->scenario == 'withCaptcha') {
    echo $form->field($model, 'verifyCode', ['options' => ['class' => 'form-group input-group input-group-lg captcha-group'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-wrench"></i></span>{input}'])->widget(Captcha::className(), ['captchaAction' => 'default/captcha', 'options' => ['placeholder' => $model->getAttributeLabel('verifyCode'), 'class' => 'form-control']/*, 'template' => '{input}{image}'*/]);
} ?>

<div class="form-group">
    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'rememberMe', ['options' => ['class' => 'pull-left']])->checkbox() ?>
        </div>
        <div class="col-xs-6 text-center">
            <?= Html::submitButton(\Yii::t('gromver.platform', 'Signin'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
        </div>
    </div>
</div>
<div class="form-group links">
    <?= Html::a(Yii::t('gromver.platform', 'Registration'), ['/grom/auth/default/signup'], ['class' => 'signup', 'target' => '_parent']) ?>
    <?= Html::a(Yii::t('gromver.platform', 'Forgot your password?'), ['/grom/auth/default/request-password-reset-token'], ['class' => 'forgot-password', 'target' => '_parent']) ?>
</div>

<?php ActiveForm::end(); ?>