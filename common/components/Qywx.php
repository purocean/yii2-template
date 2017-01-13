<?php
namespace common\components;

use Yii;
use application\models\Config;

class Qywx extends \yii\base\Component
{
    public $dataPath = null;
    public $corpid = null;
    public $secret = null;
    public $safe = '0';

    private $_wx = null;

    public function getWx()
    {
        if (is_null($this->_wx)) {
            $this->_wx = new \Wxsdk\Qywx([
                'safe'     => $this->safe,
                'corpid'   => Yii::$app->params['qywx']['corpid'],
                'secret'   => Yii::$app->params['qywx']['secret'],
                'dataPath' => Yii::getAlias($this->dataPath),
            ]);
        }

        return $this->_wx;
    }
}
