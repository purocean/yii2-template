<?php

use yii\db\Migration;

class m161206_105739_add_departments_auth extends Migration
{
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-permission "/departments/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child suadmin "/departments/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/add-child admin "/departments/*"');
    }

    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child suadmin "/departments/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-child admin "/departments/*"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac/remove-permission "/departments/*"');
    }
}
