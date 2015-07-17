<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var gromver\platform\core\modules\page\models\PageSearch $searchModel
 * @var string $route
 */

$this->title = Yii::t('gromver.platform', 'Select Page');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

	<?/*<h1><?= Html::encode($this->title) ?></h1>*/?>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'id' => 'grid',
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var \gromver\platform\core\modules\page\models\Page $model */
                    return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'status',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\page\models\Page */
                    return $model->getStatusLabel();
                },
                'width' => '100px',
                'filter' => \gromver\platform\core\modules\page\models\Page::statusLabels()
            ],
            [
                'attribute' => 'tags',
                'width' => '120px',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var $model \gromver\platform\core\modules\page\models\Page */
                    return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title'),
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/grom/tag/backend/default/tag-list']),
                        ],
                    ],
                ]
            ],
            [
                'header' => Yii::t('gromver.platform', 'Action'),
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) use ($route) {
                    /** @var $model \gromver\platform\core\modules\page\models\Page */
                    return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \gromver\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('gromver.platform', 'Page: {title}', ['title' => $model->title]),
                            'route' => \gromver\platform\core\modules\menu\models\MenuItem::toRoute($route, ['id' => $model->id]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->alias
                        ]),
                    ]);
                },
                'width' => '80px',
                'mergeHeader' => true,
                'format' => 'raw'
            ]
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 0],
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . '</h3>',
            'type' => 'info',
            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), [null], ['class' => 'btn btn-info']),
            'showFooter' => false,
        ],
	]) ?>

</div>