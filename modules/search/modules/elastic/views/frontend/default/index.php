<?php

/* @var $this yii\web\View */
/* @var $query string */

/** @var \gromver\platform\core\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Search');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Search');
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="search-default-index">
    <h1><?= $this->title ?></h1>
    <?php echo \gromver\platform\core\modules\search\widgets\SearchFormFrontend::widget([
        'id' => 'fElasticForm',
        'url' => '',
        'query' => $query,
        'configureAccess' => 'none'
    ]);

    echo \gromver\platform\core\modules\search\modules\elastic\widgets\SearchResultsFrontend::widget([
        'id' => 'fElasticResults',
        'query' => $query,
    ]); ?>
</div>
