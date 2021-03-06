<?php
/**
 * @var $this yii\web\View
 * @var $widget gromver\platform\core\modules\main\widgets\PlatformPanel
 */

use gromver\platform\core\Application;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->registerAssetBundle(\gromver\platform\core\modules\main\widgets\assets\PlatformAsset::className());

$navBar = NavBar::begin(\yii\helpers\ArrayHelper::merge([
    'brandLabel' => Yii::$app->name,
    'brandUrl' => ['/main/backend/default/index'],
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top platform-panel platform-panel_backend'
    ],
], $widget->options)); ?>

<?= \gromver\platform\core\modules\search\widgets\SearchFormBackend::widget([
    'id' => 'bPanelForm',
    'options' => ['class' => 'navbar-form'],
    'wrapperOptions' => ['class' => 'navbar-left'],
    'query' => '',
    'context' => '',
]); ?>

<?php if (Yii::$app->user->can('customizeWidget')) { ?>
    <div class="input-group navbar-left">
        <?= Html::tag('span', Yii::t('gromver.platform', 'Editing mode'), ['class' => 'navbar-text']) . '&nbsp;' ?>
        <div class="btn-group">
            <?php if (Yii::$app->mode === Application::MODE_EDIT) {
                echo Html::button(Yii::t('gromver.platform', 'On'), ['class'=>'btn btn-success navbar-btn btn-xs active']);
                echo Html::a(Yii::t('gromver.platform', 'Off'), ['/main/backend/default/mode', 'mode' => Application::MODE_VIEW, 'backUrl' => Yii::$app->request->getUrl()], ['class'=>'btn btn-default navbar-btn btn-xs']);
            } else {
                echo Html::a(Yii::t('gromver.platform', 'On'), ['/main/backend/default/mode', 'mode' => Application::MODE_EDIT, 'backUrl' => Yii::$app->request->getUrl()], ['class'=>'btn btn-default navbar-btn btn-xs']);
                echo Html::button(Yii::t('gromver.platform', 'Off'), ['class'=>'btn btn-success navbar-btn btn-xs active']);
            } ?>
        </div>
    </div>
<?php }

$menuItems = [
    ['label' => Yii::t('gromver.platform', 'System'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Control Panel'), 'url' => ['/main/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Configuration'), 'url' => ['/main/backend/default/params']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['/user/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Flush Cache'), 'url' => ['/main/backend/default/flush-cache']],
    ]],
    ['label' => Yii::t('gromver.platform', 'Menu'), 'items' => array_merge([
        ['label' => Yii::t('gromver.platform', 'Menu Types'), 'url' => ['/menu/backend/type/index']],
        ['label' => Yii::t('gromver.platform', 'Menu Items'), 'url' => ['/menu/backend/item/index']],
        '<li class="divider"></li>',
    ], array_map(function ($value) {
        /** @var $value \gromver\platform\core\modules\menu\models\MenuType */
        return ['label' => $value->title, 'url' => ['/menu/backend/item/index', 'MenuItemSearch' => ['menu_type_id' => $value->id]]];
    }, \gromver\platform\core\modules\menu\models\MenuType::find()->all()))],
    /*['label' => Yii::t('gromver.platform', 'Content'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/page/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/news/backend/category/index']],
        ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/news/backend/post/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Tags'), 'url' => ['/tag/backend/default/index']],
        '<li class="divider"></li>',
        ['label' => Yii::t('gromver.platform', 'Media Manager'), 'url' => ['/media/backend/default/index']],
    ]],
    ['label' => Yii::t('gromver.platform', 'Components'), 'items' => [
        ['label' => Yii::t('gromver.platform', 'Version Manager'), 'url' => ['/version/backend/default/index']],
        ['label' => Yii::t('gromver.platform', "Widget's Settings"), 'url' => ['/widget/backend/default/index']],
        ['label' => Yii::t('gromver.platform', 'Search'), 'url' => ['/sqlsearch/backend/default/index']],
    ]],*/
];
if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => Yii::t('gromver.platform', 'Login'), 'url' => Yii::$app->user->loginUrl];
} else {
    $menuItems[] = [
        'label' => '<i class="glyphicon glyphicon-user"></i> ' . Yii::$app->user->identity->username,
        'items' => [
            ['label' => '<i class="glyphicon glyphicon-home"></i> ' . Yii::t('gromver.platform', 'Home'), 'url' => Yii::$app->homeUrl],
            ['label' => '<i class="glyphicon glyphicon-envelope"></i> ' . Yii::t('gromver.platform', 'Contact'), 'url' => ['/main/backend/default/contact']],
            '<li class="divider"></li>',
            ['label' => '<i class="glyphicon glyphicon-cog"></i> ' . Yii::t('gromver.platform', 'Account'), 'url' => ['/user/backend/account/index']],
            ['label' => '<i class="glyphicon glyphicon-log-out"></i> ' . Yii::t('gromver.platform', 'Logout'), 'url' => ['/auth/default/logout']]
        ]
    ];
} ?>
<div class="navbar-right">

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
        'encodeLabels' => false
    ]) ?>

</div>
<?php NavBar::end() ?>
