<?php
/**
 * @var $this yii\web\View
 * @var $model gromver\platform\core\modules\page\models\Page
 * @var $rootModel gromver\platform\core\modules\page\models\Page
 */

/** @var \gromver\platform\core\modules\menu\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : $model->title;
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
    //page breadcrumbs
    if ($menu->isApplicableContext()) {
        list($route, $params) = $menu->parseUrl();
        $_breadcrumbs = $model->getBreadcrumbs(false);
        while (($crumb = array_pop($_breadcrumbs)) && $crumb['url']['id'] != $params['id']) {
            $this->params['breadcrumbs'][] = $crumb;
        }
    }
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

echo \gromver\platform\core\modules\page\widgets\PageGuide::widget([
    'id' => 'page-guide',
    'rootPage' => $rootModel,
    'page' => $model,
    'context' => Yii::$app->menuManager->activeMenu ? Yii::$app->menuManager->activeMenu->path : null,
]);

$model->hit();