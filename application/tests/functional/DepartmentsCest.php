<?php
namespace application\tests\functional;

use application\tests\FunctionalTester;
use common\fixtures\User as UserFixture;
use common\fixtures\AuthAssignment as authAssignmentFixture;
use application\fixtures\Departments as DepartmentsFixture;

class DepartmentsCest
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
            'departments' => [
                'class' => DepartmentsFixture::className(),
                'dataFile' => codecept_data_dir() . 'departments.php'
            ],
        ]);
        $I->haveHttpHeader('Content-Type', 'application/json');
    }

    public function index(FunctionalTester $I)
    {
        $I->wantTo('ensure that index api works');

        // 我是其他人
        $I->am('okirlin');
        $I->sendGET('/departments');
        $I->seeResponseMatchesJsonType(['status' => 'integer|string']);

        // 我是管理员
        $I->am('admin');
        $I->sendGET('/departments');
        $I->dontSeeResponseMatchesJsonType(['status' => 'integer|string']);
    }

    /**
     * @group wechat
     */
    public function sync(FunctionalTester $I)
    {
        $I->wantTo('ensure that sync api works');

        // 我是其他人
        $I->am('okirlin');
        $I->sendPOST('/departments/sync');
        $I->seeResponseContainsJson(['status' => 403]);

        // 我是管理员
        $I->am('admin');
        $I->sendGET('/departments/sync');
        $I->seeResponseContainsJson(['status' => 405]); // 不允许 GET

        // 我是管理员
        $I->am('admin');
        $I->sendPOST('/departments/sync');
        $I->seeResponseContainsJson(['status' => 'ok']);
    }
}
