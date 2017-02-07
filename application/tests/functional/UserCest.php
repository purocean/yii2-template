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

    public function assign(FunctionalTester $I)
    {
        $I->wantTo('ensure that assign api works');

        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/assign');
        $I->seeResponseCodeIs(403);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/assign');
        $I->seeResponseCodeIs(405);

        $I->sendPOST('/user/assign', ['username' => 'notexist']);
        $I->seeResponseContainsJson(['message' => '用户不存在']);

        $I->sendPOST('/user/assign', ['username' => 'okirlin', 'roles' => ['notexist']]);
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    /**
     * @group wechat
     */
    public function syncAndSendmsg(FunctionalTester $I)
    {
        $I->wantTo('ensure that sync & sendmsg api works');

        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/sync');
        $I->seeResponseCodeIs(403);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/sync');
        $I->seeResponseCodeIs(405);

        $I->sendPOST('/user/sync');
        $I->seeResponseContainsJson(['status' => 'ok']);

        // 发消息
        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/user/sendmsg');
        $I->seeResponseCodeIs(403);

        // 我是管理员，GET
        $I->am('admin');
        $I->sendGET('/user/sendmsg');
        $I->seeResponseCodeIs(405);

        $I->sendPOST('/user/sendmsg', ['username' => 'cscs', 'message' => '    ']);
        $I->seeResponseContainsJson(['message' => '消息内容不能为空']);

        $I->sendPOST('/user/sendmsg', ['username' => 'cscs', 'message' => '你好']);
        $I->seeResponseContainsJson(['status' => 'ok']);
    }

    public function qrlogin(FunctionalTester $I)
    {
        $I->wantTo('ensure that qrlogin api works');

        $I->sendPOST('/user/qrlogin');
        $I->seeResponseContainsJson(['status' => 'ok']);

        $I->sendPOST('/user/qrlogin', ['nonce' => 'zSQ+hx3yLiwt+RBEcmeoOL+POJi/7+4VkeZFE9jW2P8=']);
        $I->seeResponseContainsJson(['status' => 'error']);

        $I->sendPOST('/user/qrlogin', ['nonce' => 'zSQ+hx3yLiwt+RBEcmeoOL+POJi/7login']);
        $I->seeResponseContainsJson(['status' => 'ok']);
    }
}
