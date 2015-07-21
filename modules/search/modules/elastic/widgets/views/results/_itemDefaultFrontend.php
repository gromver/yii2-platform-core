<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \gromver\platform\core\modules\search\modules\elastic\models\Index */

echo Html::beginTag('div', ['class' => 'search-result-item']);
echo Html::a($model->highlight['title'][0], $model->getFrontendViewLink(), ['class' => 'h4 title']);
echo Html::tag('p', (Html::tag('small', Yii::$app->formatter->asDate($model->updated_at, 'd MMMM Y'), ['class' => 'date']) . ' - ') . implode(' ... ', $model->highlight['content']), ['class' => 'text']);

echo Html::endTag('div');