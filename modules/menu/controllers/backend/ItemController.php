<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\menu\controllers\backend;


use gromver\platform\core\modules\main\models\DbState;
use gromver\platform\core\modules\menu\models\MenuItem;
use gromver\platform\core\modules\menu\models\MenuItemSearch;
use gromver\platform\core\modules\menu\models\MenuLinkParams;
use kartik\widgets\Alert;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Response;

/**
 * Class ItemController implements the CRUD actions for Menu model.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */

class ItemController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post'],
                    'publish' => ['post'],
                    'unpublish' => ['post'],
                    'status' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'routers', 'select', 'item-list', 'ckeditor-select', 'ckeditor-select-component', 'ckeditor-select-menu'],
                        'roles' => ['readMenuItem'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createMenuItem'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'ordering', 'publish', 'unpublish', 'status'],
                        'roles' => ['updateMenuItem'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'bulk-delete'],
                        'roles' => ['deleteMenuItem'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all MenuItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all MenuItem models with linkType == LINK_ROUTE.
     * @return mixed
     */
    public function actionSelect()
    {
        $searchModel = new MenuItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        Yii::$app->applyModalLayout();

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Отдает список пунктов меню для Select2 виджета
     * @param string|null $q
     * @param string|null $language
     * @param integer|null $exclude
     * @param integer|null $menu_type_id
     * @return array
     */
    public function actionItemList($q = null, $language = null, $exclude = null, $menu_type_id = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $query = MenuItem::find()->excludeRoots();
        if ($exclude && $item = MenuItem::findOne($exclude)) {
            /** @var $item MenuItem */
            $query->excludeItem($item);
        }

        $results = $query->select('id, title AS text')->andFilterWhere(['like', 'title', urldecode($q)])->andFilterWhere(['language' => $language, 'menu_type_id' => $menu_type_id])->limit(20)->asArray()->all();

        return ['results' => $results];
    }

    /**
     * Lists all routers.
     * @return mixed
     */
    public function actionRouters()
    {
        Yii::$app->applyModalLayout();

        return $this->render('routers');
    }

    /**
     * Используется CkEditor для выбора типа ссылки.
     * @param string $CKEditor
     * @param string $CKEditorFuncNum
     * @param string $langCode
     * @return mixed
     */
    public function actionCkeditorSelect($CKEditor, $CKEditorFuncNum, $langCode)
    {
        Yii::$app->applyModalLayout();

        return $this->render('ckeditor-select', [
            'CKEditor' => $CKEditor,
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'langCode' => $langCode
        ]);
    }
    /**
     * Используется CkEditor для выбора ссылки на компонент.
     * @param string $CKEditor
     * @param string $CKEditorFuncNum
     * @param string $langCode
     * @return mixed
     */
    public function actionCkeditorSelectComponent($CKEditor, $CKEditorFuncNum, $langCode)
    {
        Yii::$app->applyModalLayout();

        return $this->render('ckeditor-select-component', [
            'CKEditor' => $CKEditor,
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'langCode' => $langCode
        ]);
    }
    /**
     * Используется CkEditor для выбора ссылки пункт меню.
     * @param string $CKEditor
     * @param string $CKEditorFuncNum
     * @param string $langCode
     * @return mixed
     */
    public function actionCkeditorSelectMenu($CKEditor, $CKEditorFuncNum, $langCode)
    {
        Yii::$app->applyModalLayout();

        return $this->render('ckeditor-select-menu', [
            'CKEditor' => $CKEditor,
            'CKEditorFuncNum' => $CKEditorFuncNum,
            'langCode' => $langCode
        ]);
    }

    /**
     * Displays a single MenuItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MenuItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null $menuTypeId
     * @param null $parentId
     * @param string|null $backUrl
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreate($menuTypeId = null, $parentId = null, $backUrl = null)
    {
        $model = new MenuItem();
        $model->loadDefaultValues();
        $model->status = MenuItem::STATUS_PUBLISHED;
        // по дефолту выставляем ендпоинт на "временную старницу"
        $model->link_type = MenuItem::LINK_ROUTE;
        $model->link = 'main/frontend/default/dummy-page';

        if (isset($menuTypeId)) $model->menu_type_id = $menuTypeId;

        if (isset($parentId)) {
            $parentCategory = $this->findModel($parentId);
            $model->parent_id = $parentCategory->id;
        }

        $linkParamsModel = new MenuLinkParams();
        $linkParamsModel->setAttributes($model->getLinkParams());

        if ($model->load(Yii::$app->request->post()) && $linkParamsModel->load(Yii::$app->request->post()) && $model->validate() && $linkParamsModel->validate()) {
            $model->setLinkParams($linkParamsModel->toArray());
            $model->saveNode(false);

            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                    'model' => $model,
                    'linkParamsModel' => $linkParamsModel,
                ]);
        }
    }

    /**
     * Updates an existing MenuItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string|null $backUrl
     * @return mixed
     */
    public function actionUpdate($id, $backUrl = null)
    {
        $model = $this->findModel($id);

        $linkParamsModel = new MenuLinkParams();
        $linkParamsModel->setAttributes($model->getLinkParams());

        if ($model->load(Yii::$app->request->post()) && $linkParamsModel->load(Yii::$app->request->post()) && $model->validate() && $linkParamsModel->validate()) {
            $model->setLinkParams($linkParamsModel->toArray());
            $model->saveNode(false);

            return $this->redirect($backUrl ? $backUrl : ['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                    'model' => $model,
                    'linkParamsModel' => $linkParamsModel,
                ]);
        }
    }

    /**
     * Deletes an existing MenuItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->children()->count()) {
            Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', "It's impossible to remove menu item ID:{id} so far it contains descendants.", ['id' => $model->id]));
         } else {
            $model->delete();
        }

        if (Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }

        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        $models = MenuItem::find()->where(['id' => $data])->orderBy(['lft' => SORT_DESC])->all();

        foreach ($models as $model) {
            /** @var MenuItem $model */
            if ($model->children()->count()) continue;

            $model->delete();
        }

        if (!Yii::$app->request->getIsAjax()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
        }
    }

    /**
     * @return Response
     */
    public function actionOrdering()
    {
        $data = Yii::$app->request->getBodyParam('data', []);

        foreach ($data as $id => $order) {
            if ($target = MenuItem::findOne($id)) {
                $target->updateAttributes(['ordering' => intval($order)]);
            }
        }

        MenuItem::find()->roots()->one()->reorderNode('ordering');
        DbState::updateState(MenuItem::tableName());

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionPublish($id)
    {
        $model = $this->findModel($id);

        $model->status = MenuItem::STATUS_PUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUnpublish($id)
    {
        $model = $this->findModel($id);

        $model->status = MenuItem::STATUS_UNPUBLISHED;
        $model->save();

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @param $status
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionStatus($id, $status)
    {
        $model = $this->findModel($id);

        $model->status = $status;
        if (!$model->save()) {
            Yii::$app->session->setFlash(Alert::TYPE_DANGER, $model->getFirstError('status'));
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * Finds the MenuItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MenuItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MenuItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
