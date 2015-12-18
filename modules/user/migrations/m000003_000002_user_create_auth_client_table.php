<?php

use yii\db\Schema;

class m000003_000002_user_create_auth_client_table extends \yii\db\Migration
{
    public function up()
    {
        // auth client
        $this->createTable('{{%core_user_auth_client}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'source' => Schema::TYPE_STRING . '(255) NOT NULL',
            'source_id' => Schema::TYPE_STRING . '(255) NOT NULL',
        ]);

        $this->createIndex('UserId_idx', '{{%core_user_auth_client}}', 'user_id');
        $this->addForeignKey('Core_AuthClient_UserId_fk', '{{%core_user_auth_client}}', 'user_id', '{{%core_user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%core_user_auth_client}}');
    }
}