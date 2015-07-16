<?php

use yii\db\Schema;
use yii\db\Migration;

class m000000_000000_app_init extends Migration
{
    public function up()
    {
        // создаем дефолтные конфигурационные файлы
        \yii\helpers\FileHelper::createDirectory(Yii::getAlias('@app/config/grom/params'), 0777);

        // конфиг консольного приложения
        $filePath = Yii::getAlias('@app/config/grom/console.php');
        file_put_contents($filePath, '<?php return [];');
        @chmod($filePath, 0775);

        // конфиг веб приложения
        $filePath = Yii::getAlias('@app/config/grom/web.php');
        file_put_contents($filePath, '<?php return [];');
        @chmod($filePath, 0775);
    }

    public function down()
    {
        // удаляем конфиг файлы
        \yii\helpers\FileHelper::removeDirectory(Yii::getAlias('@app/config/grom'));
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
