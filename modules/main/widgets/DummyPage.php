<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main\widgets;


use gromver\platform\core\modules\widget\widgets\Widget;
use Yii;
use yii\helpers\Html;

/**
 * Class DummyPage
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DummyPage extends Widget
{
    /**
     * Dummy page description
     * @var string
     * @field textarea
     * @translation gromver.platform
     */
    public $message;

    protected function launch()
    {
        if (!$this->message) {
            $this->message = Yii::t('gromver.platform', 'This page is under construction.');
        }

        echo Html::tag('h1', $this->view->title, ['class' => 'page-title title-dummy-page']);

        echo Html::tag('p', $this->message, ['class' => 'dummy-page-message']);
    }

    public function customControls()
    {
        if ($activeMenu = Yii::$app->menuManager->activeMenu) {
            return [
                [
                    'url' => ['/menu/backend/item/update', 'id' => $activeMenu->id, 'backUrl' => $this->getBackUrl()],
                    'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                    'options' => ['title' => Yii::t('gromver.platform', 'Update Page')]
                ],
            ];
        } else {
            return [];
        }
    }
}