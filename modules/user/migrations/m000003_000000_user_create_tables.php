<?php

use yii\db\Schema;

class m000003_000000_user_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // user
        $this->createTable('{{%core_user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(64) NOT NULL',
            'email' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING . '(32)',
            'auth_key' => Schema::TYPE_STRING . '(128)',
            'status' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT ' . \gromver\platform\core\modules\user\models\User::STATUS_ACTIVE,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'deleted_at' => Schema::TYPE_INTEGER,
            'last_visit_at' => Schema::TYPE_INTEGER,
            'login_ip' => Schema::TYPE_INTEGER . ' UNSIGNED',
        ]);

        $this->createIndex('Username_idx', '{{%core_user}}', 'username');
        $this->createIndex('Email_idx', '{{%core_user}}', 'email');
        $this->createIndex('Status_idx', '{{%core_user}}', 'status');

        // user_param
        $this->createTable('{{%core_user_param}}', [
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . '(50) NOT NULL',
            'value' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->addPrimaryKey('UserId_Name_pk', '{{%core_user_param}}', ['user_id', 'name']);
        $this->createIndex('Value_idx', '{{%core_user_param}}', 'value(50)');
        $this->addForeignKey('Core_UserParam_User_fk', '{{%core_user_param}}', 'user_id', '{{%core_user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%core_user_param}}');
        $this->dropTable('{{%core_user}}');
    }
}