<?php

namespace application\controllers;

use Yii;
use application\components\BaseController;
use common\components\AjaxData;
use application\models\Departments;
use application\models\DepartmentsSearch;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentsController implements the CRUD actions for Departments model.
 */
class DepartmentsController extends BaseController
{
    public $resourceName = 'departments';


    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['sync'])) {
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
                ],
            ],
        ];
    }

    /**
     * Lists all Departments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSync()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($count = Departments::sync()) {
            return AjaxData::build('ok', '已同步 ' . $count . ' 条数据');
        } else {
            return AjaxData::build('error', '同步失败');
        }
    }

    /**
     * Finds the Departments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Departments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Departments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
