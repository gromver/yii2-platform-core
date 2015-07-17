<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * Макет использутся для бэкенда
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
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
                    'url' => \yii\helpers\Url::toRoute('/main/backend/default/index')
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
            <p class="pull-left">&copy; <?= Yii::$app->grom->siteName . ' ' . date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
		</div>
	</footer>

	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
