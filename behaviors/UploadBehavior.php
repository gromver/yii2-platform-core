<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\behaviors;


use gromver\platform\core\behaviors\upload\BaseProcessor;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\validators\Validator;
use yii\web\UploadedFile;

/**
 * Class UploadBehavior
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 *
 */
class UploadBehavior extends \yii\base\Behavior
{
    /**
     * [
     *      'attribute_1' => [...settings...],
     *      'attribute_2' => [...settings...],
     * ]
     * settings: массив описывающий поле attribute
     *  - fileName: шаблон по которому будет строиться название файла, можно использовать маски
     *      Динамические, на основе своиств модели:
     *          {attrName} => $model->attrName
     *      Предустановленные, при загрузке файла
     *          #name# => название загружаемого файла
     *          #extension# => расширение загружаемого файла
     *          #attribute# => название поля для которого загружается файл
     *      Анонимная функция
     *          function($uploadedFile, $model) {}, где
     *              $uploadedFile - объект \yii\web\UploadedFile
     *              $model - модель ($this->owner)
     *      Примеры шаблонов для загружаемого файла upload.jpg
     *          #name#.#extension# => upload.jpg
     *          {id}_full.#extension# => "{$this->owner->id}_full.jpg" => 123_full.jpg
     *      Если fileName не указан то будет использовано реальное имя, загружаемого файла
     *  - basePath: физический путь к директории для хранения файлов, по умолчанию "@webroot"
     *  - baseUrl: урл к директории для хранения файлов, по умолчанию "@web"
     *  - savePath: путь к директории где хранятся файлы относительно basePath, по умолчанию "upload"
     *  - fileValidator: валидатор применяемый к загружаемому файлу, в качестве параметра может быть:
     *      - название класса валидатора *FileValidator::className()
     *      - конфигурационный массив, если 'class' не указан то по умолчанию FileValidator::className()
     *      - объект *Validator
     *  - fileProcessor: постобработка файла, после сохранения, объект класса \gromver\platform\core\behaviors\upload\BaseProcessor или конфигурационный массив
     *
     * На заметку - Убирать правила валидации для данных полей в модели - в противном случае значения полей будут затиратся после обновления модели
     * @var array
     */
    public $attributes;
    public $options = [];

    private $_ignoreUpdateEvents = false;
    private static $defaultOptions = [
        'basePath' => '@webroot',
        'baseUrl' => '@web',
        'savePath' => 'upload'
    ];

    /**
     * Normalize attributes
     */
    public function normalizeAttributes()
    {
        foreach($this->attributes as $attribute=>$options) {
            if (is_int($attribute)) {
                $this->attributes[$options] = ArrayHelper::merge(self::$defaultOptions, $this->options);
            }
            else {
                $this->attributes[$attribute] = ArrayHelper::merge(self::$defaultOptions, $this->options, $options);
            }
        }
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_keys($this->attributes);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if (!count($this->attributes))
            throw new InvalidConfigException(__CLASS__.'::attributes must be set.');

        $this->normalizeAttributes();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate'
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterSave()
    {
        if ($this->_ignoreUpdateEvents) return;

        $this->populateUploadInstances();

        $update = [];

        foreach($this->attributes() as $attribute) {
            $file = $this->owner->$attribute;
            if (!$file instanceof UploadedFile) continue;

            if ($oldFileName = $this->owner->getOldAttribute($attribute)) {
                @unlink($this->getFilePath($attribute, $oldFileName));
            }

            $newFileName = $this->createFileName($attribute);
            $filePath = $this->getFilePath($attribute, $newFileName);
            $this->checkFilePath($attribute);
            if (!$file->saveAs($filePath, false)){
                Yii::warning('Saving '.$filePath.' is failed with error '.$file->error);
            }

            if ($processor = $this->getProcessor($attribute)) {
                $processor->process($filePath);
            }

            $update[$attribute] = $newFileName;
        }

        if (count($update)) {
            $this->owner->updateAttributes($update);
        }
    }

    /**
     * @param $event \yii\base\ModelEvent
     */
    public function afterDelete($event)
    {
        foreach($this->attributes() as $attribute) {
            @unlink($this->getFilePath($attribute));
        }
    }

    /**
     * @param $event \yii\base\Event
     */
    public function afterValidate($event)
    {
        $this->populateUploadInstances();

        foreach($this->attributes() as $attribute) {
            if ($validator = $this->getValidator($attribute)) {
                $validator->validateAttributes($this->owner);
            }
        }
    }

    /**
     * @param $attribute
     * @return array|null|object|Validator
     */
    private function getValidator($attribute)
    {
        if (!($validator = @$this->attributes[$attribute]['fileValidator']))
            return null;

        if ($validator instanceof Validator) {
            return $validator;
        }

        if (is_array($validator)) {
            isset($validator['class']) or $validator['class'] = FileValidator::className();
            $validator['attributes'] = $attribute;
            return $this->attributes[$attribute]['fileValidator'] = Yii::createObject($validator);
        }

        if (is_string($validator)) {
            return $this->attributes[$attribute]['fileValidator'] = Validator::createValidator($validator, $this->owner, $attribute);
        }
    }

    /**
     * @param $attribute
     * @return array|null|\gromver\platform\core\behaviors\upload\BaseProcessor
     * @throws \yii\base\InvalidConfigException
     */
    private function getProcessor($attribute)
    {
        if (!($processor = @$this->attributes[$attribute]['fileProcessor']))
            return null;

        if (is_array($processor)) {
            return $this->attributes[$attribute]['fileProcessor'] = Yii::createObject($processor);
        }

        if (is_string($processor)) {
            return $this->attributes[$attribute]['fileProcessor'] = Yii::createObject(['class'=>$processor]);
        }

        if ($processor instanceof BaseProcessor) {
            return $processor;
        }

        throw new InvalidConfigException('Обработчик файлов должен быть экземпляром класса ' . BaseProcessor::className());
    }

    /**
     * @param $attribute
     * @return mixed|string
     */
    protected function createFileName($attribute)
    {
        /** @var $file UploadedFile */
        if (($file = UploadedFile::getInstance($this->owner, $attribute)) instanceof UploadedFile) {
            //генерируем имя файла на основе шаблона
            $filename = @$this->attributes[$attribute]['fileName'];

            if ($filename instanceof \Closure)
                return $filename($file, $this->owner);

            if (is_string($filename) && !empty($filename)) {
                $fields = $this->owner->attributes();

                $search = array_map(function($value){
                    return '{' . $value . '}';
                }, $fields);

                $replace = array_map(function($attribute){
                    $value = $this->owner->$attribute;
                    return is_array($value) ? null: (string)$value;
                }, $fields);

                return str_replace(array_merge($search, ['#name#', '#extension#', '#attribute#']), array_merge($replace, [$file->getBaseName(), $file->getExtension(), $attribute]), $filename);
            }

            return $file->name;
        }
    }

    /**
     * загружет в соответсвующие поля хозяйской модели объекты UploadedFile
     */
    private function populateUploadInstances()
    {
        foreach($this->attributes() as $attribute) {
            if (!$this->owner->$attribute instanceof UploadedFile && ($instance = UploadedFile::getInstance($this->owner, $attribute)) && !$instance->getHasError()) {
                $this->owner->$attribute = $instance;
            }
        }
    }

    /**
     * @param $attribute string
     */
    private function checkFilePath($attribute)
    {
        FileHelper::createDirectory(Yii::getAlias($this->attributes[$attribute]['basePath'].DIRECTORY_SEPARATOR.$this->attributes[$attribute]['savePath'])/*, 0777*/);
    }

    /**
     * @param $attribute string
     * @return mixed|string
     */
    public function getFileName($attribute)
    {
        return $this->owner->$attribute;
    }

    /**
     * @param $attribute
     * @param null $fileName
     * @return bool|null|string
     */
    public function getFilePath($attribute, $fileName = null)
    {
        if (!($fileName or $fileName = $this->getFileName($attribute)))
            return false;

        return $fileName ? Yii::getAlias($this->attributes[$attribute]['basePath']).DIRECTORY_SEPARATOR.$this->attributes[$attribute]['savePath'].DIRECTORY_SEPARATOR.$fileName : null;
    }

    /**
     * @param $attribute
     * @param null $fileName
     * @return bool|null|string
     */
    public function getFileUrl($attribute, $fileName = null)
    {
        if (!($fileName or $fileName = $this->getFileName($attribute)))
            return false;

        return $fileName ? (Yii::getAlias($this->attributes[$attribute]['baseUrl'])?'/'.Yii::getAlias($this->attributes[$attribute]['baseUrl']):'').'/'.$this->attributes[$attribute]['savePath'].'/'.$fileName : null;
    }

    /**
     * @param $attribute string
     * @param $staticContext bool
     */
    public function deleteFile($attribute, $staticContext = false)
    {
        @unlink($this->getFilePath($attribute));
        if ($staticContext) {
            $this->owner->updateAll([$attribute => null], $this->owner->getPrimaryKey(true));
        } else {
            $this->owner->$attribute = null;
            $this->_ignoreUpdateEvents = true;
            $this->owner->save(false);
            $this->_ignoreUpdateEvents = false;
        }
    }
}