<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '登录';
?>

<style type="text/css">
    footer {
        display: none;
    }

    body {
        color: #9ea7b3!important;
        font-family:"Open Sans", Arial, sans-serif!important;
        font-size: 13px!important;
        line-height: 20px;
        overflow-x: hidden!important;
        min-height: 100%;
        z-index: -2;
        margin: 0px !important;
        background: url('/img/bg.jpg') no-repeat top center fixed;
        -moz-background-size: cover;
        -webkit-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }

    #login-wrapper {
        margin: 50px auto 0;
        position: relative;
        z-index: 5;
    }

    #logo-login {
        background: rgba(48, 65, 96, 0.8);
        border-radius: 3px 3px 0 0;
        color: #FFFFFF;
        padding: 1px 0 14px 25px;
    }

    #logo-login h1 {
        color: #FFFFFF;
        font-size: 30px;
        font-weight: 200;
        letter-spacing: -1px;
        text-decoration: inherit;
        text-transform: uppercase;
    }


    .account-box {
        -moz-border-radius: 0 0 4px 4px
        -webkit-border-radius: 0 0 4px 4px;
        -khtml-border-radius: 0 0 4px 4px;
        border-radius: 0 0 4px 4px;
        z-index: 3;
        font-size: 13px !important;
        font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;
        background-color: #ffffff;
        padding: 20px;
    }

    .or-box {
        clear: both;
        border-top: 1px solid #DFDFDF;
        margin-bottom: 0;
        padding-top: 20px;
        position: relative;
    }

    .or {
        background-color: #FFFFFF;
        color: #666666;
        position: relative;
        text-align: center;
        top: -30px;
        width: 60px;
        padding:0 10px;
    }

    .login-with {
        background: none repeat scroll 0 0 #FFFFFF;
        font-size: 12px;
        font-weight: bold;
        margin: 0 auto;
        padding: 0 10px;
        position: relative;
        text-align: center;
        top: -33px;
    }

    .login-with b{
        color: #00ACED;
    }
</style>

<div class="container">
    <div class="row" id="login-wrapper">
        <div class="col-md-4 col-md-offset-4" style="overflow: hidden; padding: 0">
            <div id="switch-way" class="row" style="width: 200%;position: relative; left: 0; transition: all .3s cubic-bezier(.17,.67,.76,1.32);">
                <div class="col-md-6">
                    <div class="row" style="padding: 15px">
                        <div id="logo-login">
                            <h1><?= \Yii::$app->params['siteName'] ?></h1>
                        </div>
                        <div class="account-box">
                            <div class="row">
                                <div class="col-md-12 row-block">
                                    <h4 style="text-align:center" id="login-msg">还未扫码</h4>
                                    <div id="qrcode" style="width:270px;margin:auto;"></div>
                                </div>
                            </div>
                            <hr>
                            <center>
                                <span class="text-center login-with">打开手机 <b>微信</b> 点击 <b>发现</b> 使用 <b>扫一扫</span>
                            </center>
                            <div class="row-block">
                                <div class="row">
                                    <div class="col-md-12 row-block">
                                        <button type="button" onclick="$('#switch-way').css('left', '-100%')" class="btn btn-success btn-block">账号密码登录</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row-block">
                                <div class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row" style="padding: 15px">
                        <div id="logo-login">
                            <h1><?= \Yii::$app->params['siteName'] ?></h1>
                        </div>
                        <div class="account-box">
                            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>


                                <div class="form-group">
                                    <?= $form->field($model, 'username')->textInput(['autofocus' => false])->label('用户名') ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'password')->passwordInput()->label('密码') ?>
                                </div>
                                <div class="checkbox  pull-left">
                                    <label>
                                        <input type="checkbox" name="rememberMe"> 记住用户名
                                    </label>
                                </div>
                                <div>
                                    <button class="btn btn-primary pull-right" type="submit">
                                        登 录
                                    </button>
                                </div>
                            <?php ActiveForm::end(); ?>
                            <div class="row-block">
                                <div class="row">
                                    <div class="col-md-12 row-block">
                                        <button type="button" onclick="$('#switch-way').css('left', '0')" class="btn btn-success btn-block">微信扫码登录</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJsFile('/js/qrcode.js', ['depends' => 'application\assets\AppAsset']) ?>
<script>
<?php $this->beginBlock('js') ?>

$(function () {
    checkLogin = function (nonce) {
        $.ajax({
            url: '<?= \Yii::$app->urlManager->createUrl(['user/qrlogin']) ?>',
            type: 'POST',
            dataType: 'json',
            data: {nonce: nonce},
        })
        .done(function(result) {
            if (result.status === 'ok') {
                clearInterval(timer);
                location.href = '<?= \Yii::$app->urlManager->createUrl(['site/index']) ?>';
            } else {
                result.errors && result.errors.nonce && $('#login-msg').text(result.errors.nonce[0]);
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    };

    $.ajax({
        url: '<?= \Yii::$app->urlManager->createUrl('/user/qrlogin') ?>',
        type: 'GET',
        dataType: 'json',
    })
    .done(function(result) {
        if (result.status === 'ok') {
            $("#qrcode").qrcode({
                render: "canvas",
                width: 270,
                height: 270,
                text: result.data.url,
            });

            timer = setInterval(function() {
                checkLogin(result.data.nonce);
            }, 3000);
        } else {
            alert(result.message);
        }
    })
    .fail(function() {
        alert('网络错误！')
    })
    .always(function() {
    });

    if ($('input[type=password]').attr('value')) {
        $('#switch-way').css('left', '-100%');
    }
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
