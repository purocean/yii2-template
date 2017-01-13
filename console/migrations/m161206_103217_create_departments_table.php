<?php

use yii\db\Migration;

/**
 * Handles the creation of table `departments`.
 */
class m161206_103217_create_departments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%departments}}', [
            'id'         => $this->primaryKey(),

            'name'       => $this->string()->notNull()->defaultValue('')->comment('名称'),
            'parentid'   => $this->integer()->notNull()->defaultValue(0)->comment('父级ID'),
            'order'      => $this->integer()->notNull()->defaultValue(0)->comment('排序'),

            'status'     => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%departments}}');
    }
}
