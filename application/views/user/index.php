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
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</script>
