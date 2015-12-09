<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\core\modules\page\models\Page */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="page-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'title', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 1024]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'alias', ['wrapperOptions' => ['class' => 'col-sm-9']])->textInput(['maxlength' => 255, 'placeholder' => Yii::t('gromver.platform', 'Auto-generate')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'status', ['wrapperOptions' => ['class' => 'col-sm-9']])->dropDownList($model->statusLabels()) ?>
        </div>
        <div class="col-sm-6">
            <?php
            //$("#page-parent_id").select2("data", {id: data.id, text: data.title}) - в ajax режиме не работает, решение через DOM
            $idParent_id = Html::getInputId($model, 'parent_id');
            echo $form->field($model, 'parent_id', [
                'wrapperOptions' => ['class' => 'col-sm-9'],
                'inputTemplate' => '<div class="input-group select2-bootstrap-append">{input}' . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Select Page'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-folder-open"></i>',
                        'url' => ['select', 'modal' => true, 'PageSearch[excludePage]' => $model->isNewRecord ? null : $model->id],
                        'dataHandler' =>
<<<JS
function(data) {
    $("#{$idParent_id}").html('<option value="' + data.id + '">' + data.title + '</option>').val(data.id).trigger('change');
}
JS
                        ,
                    ]) . '</div>',
            ])->widget(\kartik\select2\Select2::className(), [
                'initValueText' => $model->parent ? ($model->parent->isRoot() ? Yii::t('gromver.platform', 'Top Level') : $model->parent->title) : null,
                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => Yii::t('gromver.platform', 'Top Level'),
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['page-list', 'exclude' => $model->isNewRecord ? null : $model->id]),
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?php
            $idTags = Html::getInputId($model, 'tags');
            $handlerJs = <<<JS
function(data) {
    var select = $("#{$idTags}").append('<option value="' + data.id + '">' + data.title + '</option>'),
        selectedValues = select.val() || [];
        selectedValues.push(data.id);

    select.val($.unique(selectedValues)).trigger('change');
}
JS;
            echo $form->field($model, 'tags', [
                'wrapperOptions' => ['class' => 'col-sm-9'],
                'inputTemplate' => '<div class="input-group select2-bootstrap-append">{input}' . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Select Tag'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-folder-open"></i>',
                        'url' => ['/tag/backend/default/select', 'modal' => true],
                        'dataHandler' => $handlerJs,
                    ]) . \gromver\widgets\ModalIFrame::widget([
                        'options' => [
                            'class' => 'input-group-addon',
                            'title' => \Yii::t('gromver.platform', 'Add Tag'),
                        ],
                        'label' => '<i class="glyphicon glyphicon-plus"></i>',
                        'url' => ['/tag/backend/default/create', 'modal' => true],
                        'dataHandler' => $handlerJs,
                    ]) . '</div>',
            ])->widget(\kartik\select2\Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'),
                'options' => [
                    'multiple' => true
                ],
                'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                'pluginOptions' => [
                    'multiple' => true,
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/tag/backend/default/tag-list']),
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'ordering', ['horizontalCssClasses' => ['wrapper' => 'col-xs-8 col-sm-4', 'label' => 'col-xs-4 col-sm-3']])->textInput() ?>
        </div>
    </div>

    <?//описание версии удобнее выставлять в списках версий
    //= $form->field($model, 'versionNote')->textInput() ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#main-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Description') ?></a></li>
        <li><a href="#advanced-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Preview') ?></a></li>
        <li><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'SEO') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="main-options" class="tab-pane active">
            <?= $form->field($model, 'detail_text', [
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-9'
                ],
                'inputOptions' => ['class' => 'form-control']
            ])->widget(\gromver\platform\core\modules\main\widgets\HtmlEditor::className(), [
                'id' => 'backend-editor',
                'context' => Yii::$app->controller->getUniqueId(),
                'model' => $model,
                'attribute' => 'detail_text'
            ]) ?>
        </div>

        <div id="advanced-options" class="tab-pane">
            <?= $form->field($model, 'preview_text')->textarea(['rows' => 10]) ?>
        </div>

        <div id="meta-options" class="tab-pane">
            <?= $form->field($model, 'metakey')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2048]) ?>

            <?= $form->field($model, 'metaimg')->widget(\mihaildev\elfinder\InputFile::className(), [
                'language'      => Yii::$app->language,
                'controller'    => 'media/manager',
                'filter'        => 'image',
                'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                'options'       => ['class' => 'form-control'],
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple'      => false
            ]); ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'lock') ?>

    <div>
        <?php if ($model->isNewRecord) {
            echo Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
        } else {
            echo Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            echo ' ';
            echo \gromver\platform\core\modules\version\widgets\Versions::widget([
                'model' => $model
            ]);
        } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>