<?php
namespace application\controllers;

use Yii;
use common\models\Logs;
use application\components\MobileBaseController;

/**
 * Mobile controller
 */
class MobileController extends MobileBaseController
{
    public function actionQrlogin($nonce)
    {
        Logs::add(Yii::$app->user->id, 'user', 'qrlogin_'.$nonce, [
            'content' => '二维码登录',
            'isAllow' => false,
            'nonce' => $nonce,
            'username' => Yii::$app->user->identity->username,
        ]);

        return $this->render('qrlogin', [
            'title' => '二维码登录',
            'name' => Yii::$app->user->identity->name,
            'nonce' => $nonce,
        ]);
    }

    public function actionWxjs()
    {
        $jsApiPackage = Yii::$app->qywx->wx->getJsApiPackage();
        header('Content-Type: application/javascript');
        return <<< JS
wx.config({
    debug: false,
    appId: '{$jsApiPackage["corpid"]}',
    timestamp: {$jsApiPackage["timestamp"]},
    nonceStr: '{$jsApiPackage["nonceStr"]}',
    signature: '{$jsApiPackage["signature"]}',
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareQZone',
        'startRecord',
        'stopRecord',
        'onVoiceRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'onVoicePlayEnd',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'translateVoice',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard',
    ]
});

wx.ready(function() {
    wx.hideOptionMenu();
});

JS;
    }
}
