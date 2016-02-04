<?php

/* @var $this yii\web\View */
/* @var $model \gromver\platform\core\modules\main\models\ContactForm */

?>

<div><?= $model->body ?></div>

<br/>

<hr/>

<div><small><?= Yii::t('gromver.platform', 'This e-mail has been sent by <b>{name}</b>, e-mail <b>{email}</b>', ['name' => $model->name, 'email' => $model->email]) ?></small></div>