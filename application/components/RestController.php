<?php
namespace application\components;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use application\components\RestController;

use common\components\AjaxData;

class RestController extends ActiveController
{
    public $modelClass = '';
    public $resourceName = '';

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (parent::beforeAction($action)) {
            $this->checkAccess($this->action->id);

            return true;
        } else {
            return false;
        }
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['index', 'save', 'delete', 'view'])) {
            if (!Yii::$app->user->can("/{$this->resourceName}/*")) {
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
                    'except' => ['options'],
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
                        'delete' => ['delete'],
                    ],
                ]
            ]
        );
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['delete'], $actions['create'], $actions['update']);
        return $actions;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return AjaxData::build('ok', '获取成功', $model);
    }

    public function actionSave($data = null)
    {
        $className = $this->modelClass;

        is_null($data) and $data = Yii::$app->request->getBodyParams();
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        if ($id) {
            if (!$model = $className::findOne($id)) {
                return AjaxData::build('error', '不存在的记录');
            } else {
                $model->setAttributes($data);
            }
        } else {
            $model = new $className($data);
        }

        if ($model->save()) {
            return AjaxData::build('ok', '保存成功');
        } else {
            return AjaxData::build('error', '保存失败', null, $model->errors);
        }
    }

    public function actionDelete($id)
    {
        $className = $this->modelClass;

        $count = 0;
        foreach (explode(',', $id) as $id) {
            $model = $this->findModel($id);
            $model->status = $className::STATUS_DELETED;
            $model->save() and ++$count;
        }

        return AjaxData::build('ok', "成功删除 {$count} 条数据");
    }

    protected function findModel($id)
    {
        $className = $this->modelClass;

        if (($model = $className::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('该条记录不存在');
        }
    }
}
