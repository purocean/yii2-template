<?php
namespace application\models;

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

        $fields[] = 'roles';
        $fields[] = 'rolesStr';

        return $fields;
    }

    public function getRoles()
    {
        return array_keys(Yii::$app->authManager->getRolesByUser($this->id));
    }

    public function getRolesStr()
    {
        $roles = $this->roles;
        return array_intersect_key(Yii::$app->params['roles'], array_combine($roles, $roles));
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

    /**
     * 从微信同步
     *
     * @return int 同步条数
     */
    public static function sync()
    {
        $count = 0;
        $wx = Yii::$app->qywx->wx;

        $departments = Departments::find()->select('id, name')->indexBy('id')->all();
        $members = array_column(
            (array) $wx->getDepartmentMembers(Yii::$app->params['qywx']['rootid'], true, true),
            null,
            'userid'
        );

        foreach ($members as $user) {
            // 只同步出新用户
            if (self::find()->where(['username' => $user['userid']])->count() > 0) {
                continue;
            }

            $model = new self;
            $model->username = $user['userid'];
            $model->nickname = $user['name'];
            $model->email = (isset($user['email']) and $user['email']) ? $user['email'] : "{$user['userid']}@xx.com";
            $model->name = $user['name'];
            $model->mobile = isset($user['mobile']) ? $user['mobile'] : '';
            $model->department = implode(',', $user['department']);
            $model->department_name = implode(',', array_map(function ($item) use ($departments) {
                return isset($departments[$item]) ? $departments[$item]->name : '未知';
            }, $user['department']));
            $model->info = json_encode($user);

            $model->save() and ++$count;
        }

        return $count;
    }

    public static function sendMsg($username, $title, $content, $url = '')
    {
        $articles = [
            Yii::$app->qywx->wx->buildNewsItem($title, $content, $url, ''),
        ];

        if (!is_array($username)) {
            $username = [$username];
        }

        $username = array_filter($username, function ($name) {
            return $name !== 'suadmin';
        });

        return Yii::$app->qywx->wx->sendNewsMsg(
            $articles,
            ['touser' => $username],
            Yii::$app->params['qywx']['appid']
        );
    }
}
