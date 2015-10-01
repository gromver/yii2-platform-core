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
 * Interface SearchableInterface
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface SearchableInterface
{
    /**
     * @return string
     */
    public function getSearchTitle();

    /**
     * @return string
     */
    public function getSearchContent();

    /**
     * ['sport', 'policy', ...]
     * @return array
     */
    public function getSearchTags();
}