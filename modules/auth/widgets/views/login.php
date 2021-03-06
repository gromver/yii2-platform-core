<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\core\modules\user\models\User $model
 * @var string|array|null $url
 * @var string|array|null $captchaAction
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

\gromver\platform\core\modules\auth\widgets\assets\AuthAsset::register($this);
$isModal = \yii\helpers\ArrayHelper::getValue(Yii::$app->controller->actionParams, 'modal');
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
    echo $form->field($model, 'verifyCode', ['options' => ['class' => 'form-group input-group input-group-lg captcha-group'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>{input}'])->widget(Captcha::className(), ['captchaAction' => $captchaAction, 'options' => ['placeholder' => $model->getAttributeLabel('verifyCode'), 'class' => 'form-control']/*, 'template' => '{input}{image}'*/]);
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
    <?= Html::a(Yii::t('gromver.platform', 'Registration'), ['/auth/default/signup', 'modal' => $isModal], ['class' => 'signup']) ?>
    <?= Html::a(Yii::t('gromver.platform', 'Forgot your password?'), ['/auth/default/request-password-reset-token', 'modal' => $isModal], ['class' => 'forgot-password']) ?>
</div>

<?php ActiveForm::end(); ?>