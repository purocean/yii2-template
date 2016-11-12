<?php
namespace common\models;

use yii\db\ActiveRecord;

class AuthAssignment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }
}
