<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\controllers\frontend;


use gromver\platform\core\modules\page\models\Page;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \yii\web\Controller
{
    public function actionIndex()
    {
        throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->loadModel($id)
        ]);
    }

    public function actionGuide($id)
    {
        /** @var \gromver\platform\core\modules\menu\models\MenuItem $menu */
        $menu = Yii::$app->menuManager->getActiveMenu();
        $model = $this->loadModel($id);

        if ($menu->isApplicableContext()) {
            list($route, $params) = $menu->parseUrl();
            $rootModel = $this->loadModel($params['id']);
        } else {
            $rootModel = $model;
        }

        return $this->render('guide', [
            'rootModel' => $rootModel,
            'model' => $model
        ]);
    }

    public function loadModel($id)
    {
        if(!($model = Page::find()->where(['id' => $id])->published()->one())) {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }

        return $model;
    }
}
