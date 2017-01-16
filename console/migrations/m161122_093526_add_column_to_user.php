<?php

use yii\db\Migration;

class m161122_093526_add_column_to_user extends Migration
{
    public function up()
    {
        $this->addColumn(
            '{{%user}}',
            'name',
            $this->string()->after('email')->notNull()->defaultValue('')->comment('名字')
        );
        $this->addColumn(
            '{{%user}}',
            'mobile',
            $this->string(20)->after('name')->notNull()->defaultValue('')->comment('手机号码')
        );
        $this->addColumn(
            '{{%user}}',
            'department',
            $this->string()->after('mobile')->notNull()->defaultValue('')->comment('部门')
        );
        $this->addColumn(
            '{{%user}}',
            'department_name',
            $this->string()->after('department')->notNull()->defaultValue('')->comment('部门名字')
        );
        $this->addColumn(
            '{{%user}}',
            'info',
            $this->text()->after('department_name')->comment('信息')
        );
    }

    public function down()
    {
        $this->dropColumn('{{%user}}', 'name');
        $this->dropColumn('{{%user}}', 'mobile');
        $this->dropColumn('{{%user}}', 'department');
        $this->dropColumn('{{%user}}', 'department_name');
        $this->dropColumn('{{%user}}', 'info');
    }
}
