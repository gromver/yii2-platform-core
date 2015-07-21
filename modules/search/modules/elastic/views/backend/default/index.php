<?php

/* @var $this yii\web\View */
/* @var $query string */

$this->title = Yii::t('gromver.platform', 'Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-default-index">
    <h1><?= Yii::t('gromver.platform', 'Search') ?></h1>

    <?php echo \gromver\platform\core\modules\search\widgets\SearchFormBackend::widget([
        'id' => 'bElasticForm',
        'url' => '',
        'query' => $query,
        'configureAccess' => 'none'
    ]);

    echo \gromver\platform\core\modules\search\modules\elastic\widgets\SearchResultsBackend::widget([
        'id' => 'bElasticResults',
        'query' => $query,
    ]); ?>
</div>
