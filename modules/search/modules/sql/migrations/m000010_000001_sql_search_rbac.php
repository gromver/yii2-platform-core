<?php

class m000010_000001_sql_search_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на поиск (index)
        $searchPermission = $auth->createPermission('searchSql');
        $searchPermission->description = 'search sql';
        $auth->add($searchPermission);

        // setup reader role
        $reader = $auth->getRole('reader');
        $auth->addChild($reader, $searchPermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $reader = $auth->getRole('reader');

        // permissions
        $searchPermission = $auth->getPermission('searchSql');

        $auth->removeChild($reader, $searchPermission);

        $auth->remove($searchPermission);
    }
}
