<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\interfaces\model;

/**
 * Interface TranslatableInterface
 * Используется для получения данных о мультиязычности модели
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \yii\db\ActiveRecord[] $translations
 * @property string $language
 */
interface TranslatableInterface
{
    /**
     * Локализации текущей модели
     * @return static[]
     */
    public function getTranslations();

    /**
     * Язык
     * @return string
     */
    public function getLanguage();
}