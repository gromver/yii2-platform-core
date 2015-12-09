<?php

use yii\db\Migration;
use yii\db\Schema;

class m000004_000003_menu_item_add_metaimg_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%core_menu_item}}', 'metaimg', Schema::TYPE_STRING . '(1024)');
    }

    public function down()
    {
//        echo "m000004_000003_menu_item_add_metaimg_field cannot be reverted.\n";
//
//        return false;
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
