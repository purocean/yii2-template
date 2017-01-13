<?php

use yii\db\Migration;

class m161122_080245_add_user_auth extends Migration
{
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-permission "/user/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child suadmin "/user/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child admin "/user/*"');
    }

    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child suadmin "/user/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child admin "/user/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-permission "/user/*"');
    }
}
