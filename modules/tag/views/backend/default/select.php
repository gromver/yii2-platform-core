<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var gromver\platform\core\modules\tag\models\TagSearch $searchModel
 * @var string $route
 */

$this->title = Yii::t('gromver.platform', 'Select Tag');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index">

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
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\tag\models\Tag */
                    return $model->title . '<br/>' . Html::tag('small', $model->alias, ['class' => 'text-muted']);
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'group',
                'width' =>'150px',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\tag\models\Tag::find()->groupBy('group')->andWhere('[[group]] != "" AND [[group]] IS NOT NULL')->all(), 'group', 'group'),
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        /*'ajax' => [
                             'url' => \yii\helpers\Url::to(['tag-group-list']),
                         ],*/
                    ],
                ]
            ],
            [
                'attribute' => 'status',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\tag\models\Tag */
                    return $model->getStatusLabel();
                },
                'width' => '100px',
                'filter' => \gromver\platform\core\modules\tag\models\Tag::statusLabels()
            ],
            [
                'header' => Yii::t('gromver.platform', 'Action'),
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) use ($route) {
                    /** @var $model \gromver\platform\core\modules\tag\models\Tag */
                    return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \gromver\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('gromver.platform', 'Tag: {title}', ['title' => $model->title]),
                            'route' => \gromver\platform\core\modules\menu\models\MenuItem::toRoute($route, ['id' => $model->id]),
                            'link' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
                            'value' => $model->id . ':' . $model->alias
                        ]),
                    ]);
                },
                'width' => '80px',
                'mergeHeader' => true,
                'format'=>'raw'
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