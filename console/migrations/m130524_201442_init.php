<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'nickname' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string()->unique(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // migrate rbac tables
        passthru('php "' . $_SERVER['PHP_SELF']
            . '" migrate/up --migrationPath=@yii/rbac/migrations --interactive=0');

        // seed rbac
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-role suadmin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-role admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-role user');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-permission "/admin/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-permission "/dubug/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-permission "/gii/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child suadmin "/admin/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child suadmin "/dubug/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child suadmin "/gii/*"');
        passthru('php "' . $_SERVER['PHP_SELF']
            . '" rbac/add-user suadmin ' . substr(md5(time()), 0, 12)
            . ' suadmin admin@admin.admin');
    }

    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child suadmin "/gii/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child suadmin "/dubug/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child suadmin "/admin/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-permission "/gii/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-permission "/dubug/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-permission "/admin/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-role user');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-role admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-role suadmin');

        $this->dropTable('{{%user}}');
    }
}
