<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main\controllers\backend;


use gromver\models\ObjectModel;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use yii\caching\Cache;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['params', 'flush-cache', 'flush-assets', 'mode'],  //todo contact-gromver
                        'roles' => ['administrator'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'contact', 'contact-gromver'],
                        'roles' => ['administrate'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMode($mode, $backUrl = null) {
        Yii::$app->setMode($mode);

        $this->redirect($backUrl ? $backUrl : Yii::$app->request->getReferrer());
    }

    public function actionParams($type = 'main', $modal = null)
    {
        /*$paramsPath = Yii::getAlias($this->module->paramsPath);
        $paramsFile = $paramsPath . DIRECTORY_SEPARATOR . 'params.php';

        $params = $this->module->params;

        $model = new ObjectModel(MainParams::className());
        $model->setAttributes($params);*/
        /** @var \gromver\platform\core\components\ParamsObject $params */
        $params = Yii::$app->paramsManager->{$type};

        $model = new ObjectModel($params);
        $model->setAttributes($params->toArray());

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && Yii::$app->request->getBodyParam('task') !== 'refresh') {

                try {
                    $params->load($model->toArray())->save();

                    Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Configuration saved.'));

                    if ($modal) {
                        ModalIFrame::refreshParent();
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash(Alert::TYPE_DANGER, $e->getMessage());
                }
            }
        }

        if ($modal) {
            Yii::$app->applyModalLayout();
        }

        $items = [];

        foreach (Yii::$app->paramsManager->getParamsInfo() as $itemType => $info) {
            /** @var \gromver\platform\core\components\ParamsObject $params */
            $params = Yii::$app->paramsManager->{$itemType};

            $items[] = [
                'label' => $params->paramsName(),
                'url' => ['params', 'type' => $itemType, 'modal' => $modal],
                'active' => $itemType == $type
            ];
        }

        return $this->render('params', [
            'paramsMenuItems' => $items,
            'model' => $model
        ]);
    }

    protected function paramsInfoAsMenuItems($paramsInfo, $modal = null)
    {

    }

    public function actionFlushCache($component = 'cache')
    {
        /** @var Cache $cache */
        $cache = Instance::ensure($component, Cache::className());

        $cache->flush();

        Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Cache flushed.'));

        return $this->redirect(['index']);
    }

    public function actionFlushAssets()
    {
        $assetsPath = Yii::getAlias(Yii::$app->assetManager->basePath);

        if (!($handle = opendir($assetsPath))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $assetsPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
        closedir($handle);

        Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Assets flushed.'));

        return $this->redirect(['index']);
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}
