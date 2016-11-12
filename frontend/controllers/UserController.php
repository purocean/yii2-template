<?php
namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;

use frontend\components\AjaxData;

use frontend\models\LoginForm;
use frontend\models\User;

class UserController extends ActiveController
{
    public $modelClass = 'frontend\models\User';


    public function init()
    {
        parent::init();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
    }

    public function behaviors()
    {
        return array_merge(
            [
                'corsFilter' => [
                    'class' => Cors::className(),
                    'cors' => [
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Allow-Credentials' => null,
                        'Access-Control-Max-Age' => 86400,
                        'Access-Control-Expose-Headers' => [
                            'X-Pagination-Current-Page',
                            'X-Pagination-Page-Count',
                            'X-Pagination-Per-Page',
                            'X-Pagination-Total-Count',
                        ],
                    ]
                ],
            ],
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['login', 'options'],
                    'authMethods' => [
                        HttpBasicAuth::className(),
                        HttpBearerAuth::className(),
                        QueryParamAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);
        return $actions;
    }

    public function actionLogin()
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $data['rememberMe'] = isset($data['rememberMe']) and $data['rememberMe'];

        $model = new LoginForm();
        $model->setAttributes($data);
        if ($model->login()) {
            $accessToken = explode('+', $model->user->access_token, 2);
            return AjaxData::build('ok', 'login successed', [
                'username' => $model->user->username,
                'access_token' => $accessToken[0],
                'expires' => (int) $accessToken[1]
            ]);
        } else {
            return AjaxData::build('error', 'login failed', null, $model->errors);
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
        return [
            'roles' => Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
            'permissions' => array_merge(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->id)),
        ];
    }

    public function actionDelete($id)
    {
        User::softDelete(['id' => explode(',', $id)]);
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
