<?php
namespace application\tests\functional;

use application\tests\FunctionalTester;
use common\fixtures\User as UserFixture;
use common\fixtures\Logs as LogsFixture;
use common\fixtures\AuthAssignment as authAssignmentFixture;

class UserCest
{
    function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'auth_assignment' => [
                'class' => authAssignmentFixture::className(),
                'dataFile' => codecept_data_dir() . 'auth_assignment.php'
            ],
            'logs' => [
                'class' => LogsFixture::className(),
                'dataFile' => codecept_data_dir() . 'logs.php'
            ],
        ]);
    }

    public function index(FunctionalTester $I)
    {
        $I->wantTo('ensure that index api works');

        // 我是其他人
        $I->am('okirlin');
        $I->sendGET('/user');
        $I->seeResponseContainsJson(['status' => 403]);

        // 我是管理员
        $I->am('admin');
        $I->sendGET('/user');
        $I->dontSeeResponseContainsJson(['status' => 403]);
    }

    public function save(FunctionalTester $I)
    {
        $I->wantTo('ensure that save api works');
        $I->haveHttpHeader('Content-Type', 'application/json');

        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/save');
        $I->seeResponseContainsJson(['status' => 403]);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/save');
        $I->seeResponseContainsJson(['status' => 405]);

        $I->sendPOST('/user/save', json_encode(['username' => 'notexist']));
        $I->seeResponseContainsJson(['message' => '用户不存在']);

        $I->sendPOST('/user/save', json_encode(['username' => 'okirlin', 'roles' => ['notexist']]));
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    /**
     * @group wechat
     */
    public function syncAndSendmsg(FunctionalTester $I)
    {
        $I->wantTo('ensure that sync & sendmsg api works');
        $I->haveHttpHeader('Content-Type', 'application/json');

        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/sync');
        $I->seeResponseContainsJson(['status' => 403]);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/sync');
        $I->seeResponseContainsJson(['status' => 405]);

        $I->sendPOST('/user/sync');
        $I->seeResponseContainsJson(['status' => 'ok']);

        // 发消息
        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/sendmsg');
        $I->seeResponseContainsJson(['status' => 403]);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/sendmsg');
        $I->seeResponseContainsJson(['status' => 405]);

        $I->sendPOST('/user/sendmsg', ['username' => 'cscs', 'message' => '    ']);
        $I->seeResponseContainsJson(['message' => '消息内容不能为空']);

        $I->sendPOST('/user/sendmsg', ['username' => 'cscs', 'message' => '你好']);
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    public function login(FunctionalTester $I)
    {
        $I->wantTo('ensure that login api works');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET('/user/login');
        $I->seeResponseContainsJson(['status' => 405]);

        $I->sendPOST('/user/login', json_encode(['username' => 'notexist', 'password' => 'notexist']));
        $I->seeResponseContainsJson(['status' => 'error']);

        $I->sendPOST('/user/login', json_encode(['username' => 'erau', 'password' => 'notexist']));
        $I->seeResponseContainsJson(['status' => 'error']);

        $I->sendPOST('/user/login', json_encode(['username' => 'erau', 'password' => 'password_0']));
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    public function logout(FunctionalTester $I)
    {
        $I->wantTo('ensure that logout api works');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/user/logout');
        $I->seeResponseContainsJson(['status' => 401]);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/logout');
        $I->seeResponseContainsJson(['status' => 405]);

        $I->sendPOST('/user/logout');
        $I->seeResponseContainsJson(['status' => 'ok']);

        $I->seeRecord('application\models\User', ['username' => 'admin', 'access_token' => null]);
    }

    public function qrlogin(FunctionalTester $I)
    {
        $I->wantTo('ensure that qrlogin api works');
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/user/qrlogin?nonce='.urlencode('qrlogin_zSQ+hx3yLiwt+RBEcmeoOL+POJi/7+4VkeZFE9jW2P8='));
        $I->seeResponseContainsJson(['status' => 'error']);

        $I->sendPOST('/user/qrlogin?nonce='.urlencode('zSQ+hx3yLiwt+RBEcmeoOL+POJi/7login'));
        $I->seeResponseContainsJson(['status' => 'ok']);
    }
}
