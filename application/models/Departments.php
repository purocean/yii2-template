<?php

namespace application\models;

use Yii;

/**
 * This is the model class for table "{{%departments}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parentid
 * @property integer $order
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Departments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%departments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentid', 'order', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'parentid' => '父级ID',
            'order' => '排序',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 从微信同步
     *
     * @return int 同步条数
     */
    public static function sync()
    {
        $count = 0;
        $wx = Yii::$app->qywx->wx;

        // 从根目录获取
        $departments = $wx->getDepartments(1);

        $model = new self();
        $departments and self::deleteAll(['>', 'id', 0]);
        foreach ($departments as $department) {
            $model->isNewRecord = true;

            $model->id = $department['id'];
            $model->name = $department['name'];
            $model->order = $department['order'];
            $model->parentid = $department['parentid'];

            $model->save() and ++$count;
        }

        return $count;
    }
}
