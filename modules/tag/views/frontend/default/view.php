<?php
/**
 * @var $this yii\web\View
 * @var $model gromver\platform\core\modules\tag\models\Tag
 */

/** @var \gromver\platform\core\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('gromver.platform', 'Tag: {tag}', ['tag' => $model->title]);
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('gromver.platform', 'Tag: {tag}', ['tag' => $model->title]);
    $this->params['breadcrumbs'][] = ['label' => 'Tag cloud', 'url' => ['/tag/default/index']];
}
//$this->params['breadcrumbs'][] = $this->title;
//мета теги
if ($model->metakey) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->metakey], 'keywords');
}
if ($model->metadesc) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->metadesc], 'description');
}


echo \gromver\platform\core\modules\tag\widgets\TagItems::widget([
    'id' => 'tag-items',
    'tag' => $model,
]);

$model->hit();
