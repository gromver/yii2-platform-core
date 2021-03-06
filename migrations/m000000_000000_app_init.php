<?php

use yii\db\Schema;
use yii\db\Migration;

class m000000_000000_app_init extends Migration
{
    public function up()
    {
        // создаем дефолтные конфигурационные файлы
        \yii\helpers\FileHelper::createDirectory(Yii::getAlias('@app/config/core/params'), 0777);
    }

    public function down()
    {
        // удаляем конфиг файлы
        \yii\helpers\FileHelper::removeDirectory(Yii::getAlias('@app/config/core'));
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
