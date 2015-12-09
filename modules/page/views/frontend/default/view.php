<?php
/**
 * @var $this yii\web\View
 * @var $model gromver\platform\core\modules\page\models\Page
 */

/** @var \gromver\platform\core\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : $model->title;
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = $model->title;
}
//$this->params['breadcrumbs'][] = $this->title;
//мета теги
if ($model->metakey) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $model->metakey], 'keywords');
}
if ($model->metadesc) {
    $this->registerMetaTag(['name' => 'description', 'content' => $model->metadesc], 'description');
}
if ($model->metaimg) {
    $this->registerLinkTag(['rel' => 'image_src', 'href' => $model->metaimg], 'image_src');
}


echo \gromver\platform\core\modules\page\widgets\PageView::widget([
    'id' => 'page-view',
    'page' => $model,
]);

$model->hit();