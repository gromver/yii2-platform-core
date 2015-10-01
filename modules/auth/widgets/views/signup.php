<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var gromver\platform\core\modules\auth\models\SignupForm $model
 * @var string|array|null $url
 * @var string|array|null $captchaAction
 */

\gromver\platform\core\modules\auth\widgets\assets\AuthAsset::register($this);
?>
<?php $form = ActiveForm::begin([
    'id' => 'signup-form',
    'action' => $url,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{input}",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'username', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-user"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

<?= $form->field($model, 'email', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class=" glyphicon glyphicon-envelope"></i></span>{input}'])->textInput(['placeholder' => $model->getAttributeLabel('email'), 'type' => 'email', 'autocomplete'=>'off']) ?>

<?= $form->field($model, 'password', ['options' => ['class' => 'form-group input-group input-group-lg'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>{input}'])->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'autocomplete'=>'off']) ?>

<?php if ($model->scenario == $model::SCENARIO_WITH_CAPTCHA) {
    echo $form->field($model, 'verifyCode', ['options' => ['class' => 'form-group input-group input-group-lg captcha-group'], 'template' => '<span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>{input}'])->widget(Captcha::className(), ['captchaAction' => $captchaAction, 'options' => ['placeholder' => $model->getAttributeLabel('verifyCode'), 'class' => 'form-control']]);
} ?>

<div class="form-group">
    <div class="text-center">
        <?= Html::submitButton(\Yii::t('gromver.platform', 'Signup'), ['class' => 'btn btn-primary btn-lg btn-block']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
