<?php

namespace application\components;

use Yii;
use yii\web\Controller;
use common\models\LoginForm;

class MobileBaseController extends Controller
{
    public $layout = 'mobile.php';

    protected $callstate = 'b95a8a11beaa2f1f8851bfc933a19f3a'; // 微信验证回调，随机一个字符串
    protected $needAuth = true; // 控制器是否需要验证用户信息

    private $_qywx = null;

    public function init()
    {
        if (!$this->needAuth) {
            return;
        }

        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->get('state') === $this->callstate
                and $code = Yii::$app->request->get('code')) {
                $model = new LoginForm(['code' => $code]);
                $model->setScenario($model::SCENARIO_CODELOGIN);

                $params = Yii::$app->request->get();
                unset($params['state']);
                unset($params['code']);
                if ($model->login()) {
                    // 去掉 url 中的 state 和 code 参数
                    $url = preg_replace('/&?state=.+?(&|$)/is', '', Yii::$app->request->getAbsoluteUrl());
                    $url = preg_replace('/&?code=.+?(&|$)/is', '', $url);

                    header('Location: '.$url);
                    exit;
                } else {
                    die('登录失败！');
                }
            } else {
                header('Location: '.$this->qywx->getJumpOAuthUrl(
                    Yii::$app->request->getAbsoluteUrl(),
                    $this->callstate
                ));
                exit;
            }
        }
    }

    public function getQywx()
    {
        if (is_null($this->_qywx)) {
            $this->_qywx = Yii::$app->qywx->wx;
        }

        return $this->_qywx;
    }
}
