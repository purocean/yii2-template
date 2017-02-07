<?php
use yii\helpers\Html;
$this->title = $title;
?>

<style>
    body {
        background-color: #f5f5f9;
    }

    .qrlogin {
        background-color:  #fff;
        margin-top: 30px;
        text-align: center;
        padding: 16px;
    }

    h1 {
        font-size: 1.5em;
    }

    img {
        width: 80px;
        height: 80px;
    }

    .name {
        font-size: 14px;
        color: #888;
    }

    button {
        color: #fff;
        background-color: #108ee9;
        border: 1px solid #108ee9;
        width: 100%;
        display: inline-block;
        outline: 0 none;
        -webkit-appearance: none;
        -webkit-box-sizing: border-box;
        padding: 0;
        text-align: center;
        font-size: 20px;
        height: 50px;
        line-height: 50px;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
        -webkit-background-clip: padding-box;
    }
</style>

<div class="qrlogin">
    <img src="/img/info.png" />
    <h1>扫码登录</h1>
    <p class="name"><?= Html::encode($name) ?></p>
    <button id="login">确认登录</button>
</div>

<script>
<?php $this->beginBlock('js') ?>

$(function () {
    $('#login').click(function(event) {
        $(this).attr('disabled', true);
        $(this).text('请稍后……');

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['user/confirmlogin', 'nonce' => $nonce, 'allow' => '1']) ?>',
            type: 'POST',
            dataType: 'json',
        })
        .done(function(result) {
            if (result.status === 'ok') {
                WeixinJSBridge.invoke('closeWindow', {}, function(res){
                });
            } else {
                alert(result.message);
            }
        })
        .fail(function() {
            alert('网络错误！');
        })
        .always(function() {
        });

        return false;
    });
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
