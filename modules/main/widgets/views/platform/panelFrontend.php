<?php
/**
 * @var $this yii\web\View
 * @var $widget gromver\platform\core\modules\main\widgets\PlatformPanel
 */


use gromver\widgets\ModalIFrame;
use gromver\platform\core\Application;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->registerAssetBundle(\gromver\platform\core\modules\main\widgets\assets\PlatformAsset::className());

$navBar = NavBar::begin(\yii\helpers\ArrayHelper::merge([
    'brandLabel' => Yii::$app->grom->siteName,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top platform-panel'
    ],
], $widget->options)); ?>

<?= \gromver\platform\core\modules\search\widgets\SearchFormFrontend::widget([
    'id' => 'fPanelForm',
    'options' => ['class' => 'navbar-form'],
    'wrapperOptions' => ['class' => 'navbar-left'],
    'query' => '',
    'context' => '',
]); ?>

<?php if (Yii::$app->user->can('customize')) { ?>
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

if (Yii::$app->user->isGuest) { ?>
    <div class="navbar-text navbar-right">
        <i class="glyphicon glyphicon-log-in"></i>&nbsp;&nbsp;
        <?php
        $loginUrl = Yii::$app->user->loginUrl;
        $loginUrl['modal'] = 1;
        echo ModalIFrame::widget([
            'options' => [
                'class' => 'navbar-link',
            ],
            'popupOptions' => [
                'class' => 'auth-popup'
            ],
            'label' => Yii::t('gromver.platform', 'Login'),
            'url' => $loginUrl
        ]) ?>
    </div>
<?php } else {
    $items = [];
    if(Yii::$app->user->can('administrate')) {
        $items[] = ['label' => '<i class="glyphicon glyphicon-cog"></i> ' . Yii::t('gromver.platform', 'Admin Panel'), 'url' => ['/main/backend/default/index']];
        /** @var \gromver\platform\core\modules\menu\models\MenuItem $activeMenu */
        if ($activeMenu = Yii::$app->menuManager->activeMenu) {
            $items[] = ['label' => '<i class="glyphicon glyphicon-pencil"></i> ' . $activeMenu->getLinkTitle(), 'url' => ['/menu/backend/item/update', 'id' => $activeMenu->id, 'backUrl' => Yii::$app->urlManager->createUrl($activeMenu->getFrontendViewLink())]];
        }
        $items[] = '<li class="divider"></li>';
    }
    $items[] = ['label' => '<i class="glyphicon glyphicon-log-out"></i> ' . Yii::t('gromver.platform', 'Logout'), 'url' => ['/auth/default/logout']]; ?>

    <div class="navbar-right">

        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'items' => [
                [
                    'label' => '<i class="glyphicon glyphicon-user"></i> ' . Yii::$app->user->identity->username,
                    'items' => $items,
                ],
            ],
            'encodeLabels' => false
        ]) ?>

    </div>
<?php } ?>
<style>
    <?= '#' . $navBar->id ?> .navbar-right { margin-right: 0; }
</style>
<?php NavBar::end() ?>
