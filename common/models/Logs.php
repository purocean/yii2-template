<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%logs}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $model
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 */
class Logs extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%logs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Operator Id',
            'model' => 'Log Model',
            'type' => 'Log Type',
            'content' => 'Log Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function add($userId, $model, $type, $content)
    {
        if (!is_string($content)) {
            $content = json_encode($content);
        }

        $log = new self();
        $log->user_id = $userId;
        $log->model = $model;
        $log->type = $type;
        $log->content = $content;

        return $log->save(false);
    }
}
