<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var gromver\platform\core\modules\menu\models\MenuItemSearch $searchModel
 */

$this->title = Yii::t('gromver.platform', 'Select Menu Item');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

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
                'attribute' => 'language',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px',
                'filter' => Yii::$app->getAcceptedLanguagesList()
            ],
            [
                'attribute' => 'menu_type_id',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '120px',
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\menu\models\MenuItem */
                    return $model->menuType->title;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\menu\models\MenuType::find()->all(), 'id', 'title')
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                        /** @var $model \gromver\platform\core\modules\menu\models\MenuItem */
                        return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', $model->path, ['class' => 'text-muted']);
                    },
                'format' => 'html'

            ],
			[
                'attribute' => 'status',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\menu\models\MenuItem */
                    return $model->getStatusLabel();
                },
                'width' => '150px',
                'filter' => \gromver\platform\core\modules\menu\models\MenuItem::statusLabels()
            ],
            [
                'header' => Yii::t('gromver.platform', 'Action'),
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model) {
                    /** @var $model \gromver\platform\core\modules\menu\models\MenuItem */
                    return Html::a(Yii::t('gromver.platform', 'Select'), '#', [
                        'class' => 'btn btn-primary btn-xs',
                        'onclick' => \gromver\widgets\ModalIFrame::postDataJs([
                            'id' => $model->id,
                            'title' => $model->title,
                            'description' => Yii::t('gromver.platform', 'Menu Item: {title}', ['title' => $model->title]),
                            'route' => Yii::$app->urlManager->createUrl($model->getFrontendViewLink()),
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