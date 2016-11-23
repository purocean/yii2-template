<?php

use yii\db\Migration;

/**
 * Handles the creation of table `logs`.
 */
class m161123_050200_create_logs_table extends Migration
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

        $this->createTable('{{%logs}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->defaultValue(0)->notNull()->comment('Operator Id'),
            'model'      => $this->string()->defaultValue('')->notNull()->comment('Log Model'),
            'type'      => $this->string()->defaultValue('')->notNull()->comment('Log type'),

            'content'    => $this->text()->notNull()->defaultValue('')->comment('Log Content'),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%logs}}');
    }
}
