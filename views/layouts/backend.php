<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * Макет использутся для бэкенда
 * @var \yii\web\View $this
 * @var string $content
 */
\gromver\platform\core\assets\BackendAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
	<?php $this->beginBody() ?>
	<div class="wrap">
        <?= \gromver\platform\core\modules\main\widgets\PlatformPanel::widget([
            'layout' => 'platform/panelBackend',
        ]) ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'homeLink' => [
                    'label' => Yii::t('yii', 'Home'),
                    'url' => Yii::$app->homeUrlBackend,
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $body)
                echo \kartik\alert\Alert::widget([
                    'type' => $type,
                    'body' => $body
                ]) ?>
            <?= $content ?>
		</div>
	</div>

	<footer class="footer">
		<div class="container">
            <p class="pull-left">&copy; <?= Yii::$app->siteName . ' ' . date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
		</div>
	</footer>

	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
