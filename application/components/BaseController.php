<?php
namespace application\components;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class BaseController extends Controller
{
    public $resourceName = '';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->checkAccess($this->action->id);

            return true;
        } else {
            return false;
        }
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['index', 'update', 'delete', 'view'])) {
            if (!Yii::$app->user->can("/{$this->resourceName}/*")) {
                throw new ForbiddenHttpException('无权访问资源');
            }
        }
    }
}
