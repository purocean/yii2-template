<?php
namespace frontend\models;

use Yii;
use Yii\db\Expression;

class User extends \common\models\User
{
    public function fields()
    {
        $fields = parent::fields();
        // remove fields that contain sensitive information
        unset(
            $fields['access_token'],
            $fields['auth_key'],
            $fields['password_hash'],
            $fields['password_reset_token']
        );

        return $fields;
    }

    public static function softDelete($condition, $params = [])
    {
        return self::updateAll([
            'status' => self::STATUS_DELETED,
            'username' => (new Expression("concat(`username`, '_deleted')")),
            'access_token' => '',
            'password_reset_token' => '',
            'email' => (new Expression("concat(`email`, '_deleted')")),
        ], $condition, $params);
    }
}
