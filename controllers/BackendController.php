<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\controllers;


use Yii;

/**
 * Class BackendController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class BackendController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        Yii::$app->applyBackendLayout();

        if (!Yii::$app->user->can('administrate')) {
            Yii::$app->user->loginRequired();
        }

        return parent::beforeAction($action);
    }
} 