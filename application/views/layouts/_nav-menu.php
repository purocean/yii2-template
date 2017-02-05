<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

use frontend\models\Tasks;

NavBar::begin([
    'brandLabel' => Yii::$app->params['siteName'],
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
]);

$controllerId = \Yii::$app->controller->id;
$actionId = \Yii::$app->controller->action->id;
$queryParams = \Yii::$app->request->queryParams;

$menuItems = [
    [
        'label' => '用户管理',
        'url' => ['/user/index'],
        'visible' => Yii::$app->user->can('/user/*'),
    ],
    [
        'label' => '部门管理',
        'url' => ['/departments/index'],
        'visible' => Yii::$app->user->can('/user/*'),
    ],
];

if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
} else {
    $username = Yii::$app->user->identity->nickname
        ? Yii::$app->user->identity->nickname
        : Yii::$app->user->identity->username;
    $menuItems[] = '<li>'
        . Html::beginForm(['/site/logout'], 'post')
        . Html::submitButton(
            '退出 (' . Html::encode($username) . ')',
            ['class' => 'btn btn-link logout']
        )
        . Html::endForm()
        . '</li>';
}
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => $menuItems,
]);
NavBar::end();
