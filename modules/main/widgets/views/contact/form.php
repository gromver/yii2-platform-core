<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \gromver\platform\core\modules\main\models\ContactForm */

?>
<div class="site-contact">
    <h1 class="page-title title-contact"><?= Html::encode($this->title) ?></h1>

    <?= Html::tag('p', Yii::t('gromver.platform', 'If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.')) ?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'subject') ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
                <?php if ($model->scenario == 'withCaptcha') {
                    echo $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), ['captchaAction' => '/main/common/default/captcha', 'options' => ['class' => 'form-control'], 'template' => '<div class="row"><div class="col-xs-6">{image}</div><div class="col-xs-6">{input}</div></div>']);
                } ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('gromver.platform', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
