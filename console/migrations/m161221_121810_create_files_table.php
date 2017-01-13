<?php

use yii\db\Migration;

/**
 * Handles the creation of table `files`.
 */
class m161221_121810_create_files_table extends Migration
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

        $this->createTable('{{%files}}', [
            'id'         => $this->primaryKey(),

            'model'      => $this->string()->notNull()->defaultValue('')->comment('模型'),
            'title'      => $this->string()->notNull()->defaultValue('')->comment('标题'),
            'comment'    => $this->string()->notNull()->defaultValue('')->comment('备注'),
            'name'       => $this->string()->notNull()->comment('文件名'),
            'path'       => $this->text()->notNull()->comment('文件路径'),
            'hash'       => $this->string(32)->notNull()->comment('文件哈希'),
            'mime'       => $this->string()->notNull()->comment('文件类型'),
            'size'       => $this->integer()->notNull()->comment('文件尺寸'),

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
        $this->dropTable('{{%files}}');
    }
}
