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
use gromver\platform\core\modules\main\models\ContactForm;
use kartik\widgets\Alert;
use Yii;

/**
 * Class Contact
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Contact extends Widget
{
    /**
     * @field yesno
     * @translation gromver.platform
     */
    public $withCaptcha;
    /**
     * @translation gromver.platform
     */
    public $layout = 'contact/form';
    /**
     * @translation gromver.platform
     */
    public $successLayout = 'contact/success';
    /**
     * @var string
     * @field multiple
     * @multyfield text
     * @email
     * @placeholder Default
     * @translation gromver.platform
     */
    public $email;

    protected function launch()
    {
        $model = new ContactForm();
        if ($this->withCaptcha) {
            $model->scenario = 'withCaptcha';
        }

        if (!Yii::$app->user->isGuest) {
            /** @var \gromver\platform\core\modules\user\models\User $user */
            $user = Yii::$app->user->identity;
            $model->name = $user->getParam('name', $user->username);
            $model->email = $user->email;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(!empty($this->email) ? $this->email : Yii::$app->paramsManager->main->adminEmail)) {
                echo $this->render($this->successLayout);
                return;
            } else {
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'There was an error.'));
            }
        }

        echo $this->render($this->layout, [
            'model' => $model
        ]);
    }
}