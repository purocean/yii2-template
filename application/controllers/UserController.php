<?php

namespace application\controllers;

use Yii;
use application\components\BaseController;
use common\components\AjaxData;
use common\models\LoginForm;
use common\models\Logs;
use application\models\User;
use application\models\UserSearch;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    public $resourceName = 'user';


    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['sync', 'sendmsg', 'assign'])) {
            if (!Yii::$app->user->can("/{$this->resourceName}/*")) {
                throw new ForbiddenHttpException('无权访问资源');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'sync' => ['POST'],
                    'assign' => ['POST'],
                    'sendmsg' => ['POST'],
                    'codelogin' => ['POST'],
                    'confirmlogin' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'roles' => Yii::$app->params['roles'],
        ]);
    }

    public function actionSync()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($count = User::sync()) {
            return AjaxData::build('ok', '已同步 ' . $count . ' 条数据');
        } else {
            return AjaxData::build('error', '同步失败');
        }
    }

    public function actionSendmsg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
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

    public function actionAssign()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

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

    /**
     * 二维码 nonce 登录，GET 方法扫码地址和 nonce
     */
    public function actionQrlogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($nonce = Yii::$app->request->post('nonce')) {
            return self::Qrlogin($nonce);
        }

        // GET 方式获取 nonce 和 url
        $nonce =  base64_encode(openssl_random_pseudo_bytes(32));
        $url = Yii::$app->urlManager->createAbsoluteUrl(['mobile/qrlogin', 'nonce' => $nonce]);

        return AjaxData::build('ok', '获取成功', [
            'nonce' => $nonce,
            'url' => $url,
        ]);
    }

    /**
     * 确认扫码登录
     */
    public function actionConfirmlogin($nonce, $allow)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
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
    }

    public static function Qrlogin($nonce)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new LoginForm(['nonce' => $nonce]);
        $model->setScenario($model::SCENARIO_QRLOGIN);
        if ($model->login()) {
            return AjaxData::build('ok', 'login successed', [
                'name' => $model->user->name,
                'username' => $model->user->username,
            ]);
        }

        return AjaxData::build('error', 'login failed', null, $model->errors);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
