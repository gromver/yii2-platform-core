<?php

class m000002_000001_main_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('create');
        $createPermission->description = 'create';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('read');
        $readPermission->description = 'read';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('update');
        $updatePermission->description = 'update';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('delete');
        $deletePermission->description = 'delete';
        $auth->add($deletePermission);

        // право заходить в админку (обязательная проверка для всех экшенов в BackendController)
        $administratePermission = $auth->createPermission('administrate');
        $administratePermission->description = 'administrate';
        $auth->add($administratePermission);

        // add the rule
        $authorizedRule = new \gromver\platform\core\modules\main\rules\AuthorizedRule();
        $auth->add($authorizedRule);

        //add "Authorized" role
        $authorized = $auth->createRole('authorized');
        $authorized->ruleName = $authorizedRule->name;
        $auth->add($authorized);

        // add "Reader" role
        $reader = $auth->createRole('reader');
        $auth->add($reader);
        $auth->addChild($reader, $administratePermission);
        $auth->addChild($reader, $readPermission);

        // add "Author" role
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $reader);

        // add "Editor" role
        $editor = $auth->createRole('editor');
        $auth->add($editor);
        $auth->addChild($editor, $author);
        $auth->addChild($editor, $createPermission);
        $auth->addChild($editor, $updatePermission);

        // add "Administrator" role
        $admin = $auth->createRole('administrator');
        $auth->add($admin);
        $auth->addChild($admin, $deletePermission);
        $auth->addChild($admin, $editor);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getRole('reader'));
        $auth->remove($auth->getRole('author'));
        $auth->remove($auth->getRole('editor'));
        $auth->remove($auth->getRole('administrator'));
        $auth->remove($auth->getRole('authorized'));
        $auth->remove($auth->getPermission('create'));
        $auth->remove($auth->getPermission('read'));
        $auth->remove($auth->getPermission('update'));
        $auth->remove($auth->getPermission('delete'));
        $auth->remove($auth->getPermission('administrate'));
        $auth->remove($auth->getRule('isAuthorized'));
    }
}
