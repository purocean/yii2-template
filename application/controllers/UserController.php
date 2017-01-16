<?php
namespace application\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use application\components\RestController;
use common\components\AjaxData;
use common\models\LoginForm;
use application\models\User;
use common\models\Logs;

class UserController extends RestController
{
    public $modelClass = 'application\models\User';
    public $resourceName = 'user';

    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['sync', 'sendmsg', 'stuff'])) {
            if (!Yii::$app->user->can('/user/*')) {
                throw new ForbiddenHttpException('无权访问资源');
            }
        }
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['options', 'login', 'qrlogin', 'codelogin'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'save' => ['post'],
                        'sync' => ['post'],
                        'sendmsg' => ['post'],
                        'login' => ['post'],
                        'codelogin' => ['post'],
                        'confirmlogin' => ['post'],
                        'logout' => ['post'],
                    ],
                ]
            ]
        );
    }

    public function actionIndex($key = null)
    {
        $modelName = $this->modelClass;
        $query = $modelName::find()->orderBy('id desc');
        if ($key) {
            $query->filterWhere(['like', 'username', $key])
                    ->orFilterWhere(['like', 'name', $key])
                    ->orFilterWhere(['like', 'department_name', $key])
                    ->orFilterWhere(['like', 'mobile', $key]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSave($data = null)
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);

        if ($user = User::findByUsername($data['username'])) {
            $auth = Yii::$app->authManager;

            $newRoleNames = $data['roles'];
            foreach (array_keys(Yii::$app->params['roles']) as $roleName) {
                if (!$role = $auth->getRole($roleName)) {
                    return AjaxData::build('error', '权限不存在');
                }

                $auth->revoke($role, $user->id);
                if (in_array($roleName, $newRoleNames)) {
                    $auth->assign($role, $user->id);
                }
            }

            if ($user->save()) {
                return AjaxData::build('ok', '修改成功');
            } else {
                return AjaxData::build('error', '保存失败', null, $user->errors);
            }
        } else {
            return AjaxData::build('error', '用户不存在');
        }
    }

    public function actionSync()
    {
        if ($count = User::sync()) {
            return AjaxData::build('ok', '已同步 ' . $count . ' 条数据');
        } else {
            return AjaxData::build('error', '同步失败');
        }
    }

    public function actionSendmsg()
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        if ($msg = trim($data['message'])) {
            if (User::sendMsg($data['username'], '管理员消息', $msg)) {
                return AjaxData::build('ok', '发送消息成功');
            } else {
                return AjaxData::build('error', '发送消息失败');
            }

        } else {
            return AjaxData::build('error', '消息内容不能为空');
        }
    }

    /**
     * 账号密码登录
     */
    public function actionLogin()
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $data['rememberMe'] = isset($data['rememberMe']) and $data['rememberMe'];

        $model = new LoginForm();
        $model->setAttributes($data);
        if ($model->login(true)) {
            $accessToken = explode('+', $model->user->access_token, 2);
            return AjaxData::build('ok', 'login successed', [
                'name' => $model->user->name,
                'username' => $model->user->username,
                'access_token' => $accessToken[0],
                'expires' => (int) $accessToken[1]
            ]);
        }

        return AjaxData::build('error', 'login failed', null, $model->errors);
    }

    /**
     * 二维码 nonce 登录，GET 方法扫码地址和 nonce
     */
    public function actionQrlogin($nonce = null)
    {
        if (Yii::$app->request->isPost and $nonce) {
            return self::Qrlogin($nonce);
        }

        // GET 方式获取 nonce 和 url
        $nonce =  base64_encode(openssl_random_pseudo_bytes(32));
        $url = Yii::$app->urlManager->createAbsoluteUrl('/app.html#/qrlogin/' . urlencode($nonce));

        return AjaxData::build('ok', '获取成功', [
            'nonce' => $nonce,
            'url' => $url,
        ]);
    }

    /* 微信回调 code 登录
     */
    public function actionCodelogin($code)
    {
        $model = new LoginForm(['code' => $code]);
        $model->setScenario($model::SCENARIO_CODELOGIN);
        if ($model->login(true)) {
            $accessToken = explode('+', $model->user->access_token, 2);
            return AjaxData::build('ok', 'login successed', [
                'name' => $model->user->name,
                'username' => $model->user->username,
                'access_token' => $accessToken[0],
                'expires' => (int) $accessToken[1]
            ]);
        }

        return AjaxData::build('error', 'login failed', null, $model->errors);
    }

    /**
     * 确认扫码登录
     */
    public function actionConfirmlogin($nonce, $allow = null)
    {
        if (!is_null($allow)) {
            if ($log = Logs::getLastOne(null, 'user', 'qrlogin_'.$nonce)) {
                if ($allow = ($allow == '1')) {
                    $log->content = json_encode([
                        'content' => '二维码登录',
                        'isAllow' => $allow,
                        'nonce' => $nonce,
                        'username' => Yii::$app->user->identity->username,
                    ]);

                    $log->save();

                    return AjaxData::build('ok', '登录成功');
                } else {
                    return AjaxData::build('error', '拒绝登录');
                }
            } else {
                return AjaxData::build('error', '扫码失败');
            }
        } else {
            Logs::add(Yii::$app->user->id, 'user', 'qrlogin_'.$nonce, [
                'content' => '二维码登录',
                'isAllow' => false,
                'nonce' => $nonce,
                'username' => Yii::$app->user->identity->username,
            ]);

            return AjaxData::build('ok', '扫码成功');
        }
    }

    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $user->access_token = null;
        $user->save();

        return AjaxData::build('ok');
    }

    public function actionItems()
    {
        $roles = [];
        $permissions = array_keys(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->id));

        foreach (array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) as $role) {
            $roles[$role] = Yii::$app->params['roles'][$role] ?? '其他';
        }

        array_push($permissions, '/qrlogin/*');

        return [
            'roles' => $roles,
            'permissions' => array_combine($permissions, $permissions),
        ];
    }

    public function actionStuff()
    {
        return AjaxData::build('ok', '获取成功', [
            'roles' => Yii::$app->params['roles'],
        ]);
    }

    public function actionDelete($id)
    {
        throw new NotFoundHttpException();
    }

    public static function Qrlogin($nonce)
    {
        $model = new LoginForm(['nonce' => $nonce]);
        $model->setScenario($model::SCENARIO_QRLOGIN);
        if ($model->login(true)) {
            $accessToken = explode('+', $model->user->access_token, 2);
            return AjaxData::build('ok', 'login successed', [
                'name' => $model->user->name,
                'username' => $model->user->username,
                'access_token' => $accessToken[0],
                'expires' => (int) $accessToken[1]
            ]);
        }

        return AjaxData::build('error', 'login failed', null, $model->errors);
    }
}
