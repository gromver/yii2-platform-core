<?php
/**
 * @var yii\web\View $this
 * @var gromver\platform\core\modules\user\models\User $user
 */

echo Yii::t('gromver.platform', 'For changing your password please follow the <a href="{link}">link</a>', ['link' => \yii\helpers\Url::toRoute(['/auth/default/reset-password', 'token' => $user->password_reset_token], true)]);