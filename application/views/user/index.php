<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel application\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('从企业号同步', [''], ['id' => 'sync', 'class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width: 5em;'],
            ],
            'username',
            'email:email',
            'nickname',
            'name',
            'mobile',
            'department_name',
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'contentOptions' => ['style' => 'width: 15em;'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{sendmsg} {assign}',
                'buttons' => [
                    'sendmsg' => function ($url, $model, $key) {
                        return Html::a('<span data-username="'.$model->username.'" class="sendmsg glyphicon glyphicon-comment"></span>', '');
                    },
                    'assign' => function ($url, $model, $key) {
                        return Html::a('<span data-id="'.$model->id.'" class="assign glyphicon glyphicon-user"></span>', '');
                    }
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>

<form id="sendmsg-form">
    <div class="modal fade" id="sendmsg-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">向 <b id="sendmsg-b"></b> 发送消息</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="username" />
                    <textarea name="message" rows="3" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button id="sendmsg-send" class="btn btn-primary">发送</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
<?php $this->beginBlock('js') ?>

$(function () {
    $('#sync').click(function(event) {
        $(this).attr('disabled', true);
        $(this).text('同步中……');

        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl('user/sync') ?>',
            type: 'POST',
            dataType: 'json',
        })
        .done(function(result) {
            alert(result.message);

            if (result.status === 'ok') {
                location.reload();
            }
        })
        .fail(function() {
            alert('网络错误！');
        })
        .always(function() {
        });

        return false;
    });

    $(document).on('click', '.sendmsg', function(event) {
        $('#sendmsg-modal').modal('show');
        $('#sendmsg-form textarea[name=message]').val('');
        $('#sendmsg-form input[name=username]').val($(this).data('username'));
        $('#sendmsg-b').text($(this).data('username'));

        return false;
    });

    $('#sendmsg-form').submit(function(event) {
        $('#sendmsg-send').text('发送中……');
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl('user/sendmsg') ?>',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
        })
        .done(function(result) {
            if (result.status === 'ok') {
                $('#sendmsg-modal').modal('hide');
            }
            alert(result.message);
        })
        .fail(function() {
            alert('网络错误！');
        })
        .always(function() {
            $('#sendmsg-send').text('发送');
        });

        return false;
    });
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
