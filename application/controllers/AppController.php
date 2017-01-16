<?php
namespace application\controllers;

use Yii;

/**
 * App controller
 */
class AppController extends \yii\web\Controller
{
    public function actionAuth($redirect = '')
    {
        if (Yii::$app->request->get('state') === 'WechatOAuth') { // 验证回调
            $code = Yii::$app->request->get('code');
            return $this->redirect("/app.html#/login/{$code}?redirect=" . urlencode($redirect));
        } else {
            return $this->redirect(Yii::$app->qywx->wx->getJumpOAuthUrl(Yii::$app->request->getAbsoluteUrl()));
        }
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
