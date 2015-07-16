<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 14.07.15
 * Time: 11:16
 */

namespace gromver\platform\core\traits;


trait ApplicationLanguageTrait {
    public $acceptedLanguages = ['en', 'ru'];

    /**
     * @return array
     */
    public function getAcceptedLanguagesList()
    {
        return array_combine($this->acceptedLanguages, $this->acceptedLanguages);
    }
} 