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
     * Если вьюху письма оставить пустой то будет просто отослано сообщение пользователя
     * @translation gromver.platform
     */
    public $emailLayout = '@gromver/platform/core/modules/main/widgets/views/contact/email';
    /**
     * Адреса куда будут поступать сообщения
     * @var string
     * @field multiple
     * @multyfield text
     * @email
     * @placeholder Default
     * @translation gromver.platform
     */
    public $emailTo;
    /**
     * Если не указан емеил отправителя то будет использован емеил пользователя оставившего сообщение
     * @var string
     * @email
     * @translation gromver.platform
     */
    public $emailFrom;
    /**
     * @var ContactForm
     * @ignore
     */
    public $model;

    public function init()
    {
        if (!isset($this->model)) {
            $this->model = new ContactForm();
        }

        if ($this->withCaptcha) {
            $this->model->scenario = 'withCaptcha';
        }

        if (!Yii::$app->user->isGuest) {
            /** @var \gromver\platform\core\modules\user\models\User $user */
            $user = Yii::$app->user->identity;
            $this->model->name or $this->model->name = $user->getParam('name', $user->username);
            $this->model->email or $this->model->email = $user->email;
        }
    }

    protected function launch()
    {
        $model = $this->model;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($this->sendEmail()) {
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

    protected function sendEmail()
    {
        if ($this->emailLayout) {
            return Yii::$app->mailer->compose($this->emailLayout, ['model' => $this->model])
                ->setTo(!empty($this->emailTo) ? $this->emailTo : Yii::$app->paramsManager->main->adminEmail)
                ->setFrom(!empty($this->emailFrom) ? $this->emailFrom : [$this->model->email => $this->model->name])
                ->setSubject($this->model->subject)
                ->send();
        } else {
            return Yii::$app->mailer->compose()
                ->setTo(!empty($this->emailTo) ? $this->emailTo : Yii::$app->paramsManager->main->adminEmail)
                ->setFrom(!empty($this->emailFrom) ? $this->emailFrom : [$this->model->email => $this->model->name])
                ->setSubject($this->model->subject)
                ->setTextBody($this->model->body)
                ->send();
        }
    }
}