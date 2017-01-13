<?php
namespace application\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use application\components\RestController;
use common\components\AjaxData;
use application\models\Departments;

class DepartmentsController extends RestController
{
    public $resourceName = 'departments';
    public $modelClass = 'application\models\Departments';

    public function checkAccess($action, $model = null, $params = [])
    {
        parent::checkAccess($action, $model, $params);

        if (in_array($action, ['sync'])) {
            if (!Yii::$app->user->can('/departments/*')) {
                throw new ForbiddenHttpException("Error Processing Request", 1);
            }
        }
    }

    public function actionIndex()
    {
        $modelName = $this->modelClass;
        $query = $modelName::find()->orderBy('id desc');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'sync' => ['post'],
                        'save' => ['post'],
                        'delete' => ['delete'],
                    ],
                ]
            ]
        );
    }

    public function actionSync()
    {
        if ($count = Departments::sync()) {
            return AjaxData::build('ok', '已同步 ' . $count . ' 条数据');
        } else {
            return AjaxData::build('error', '同步失败');
        }
    }
}
