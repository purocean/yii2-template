<?php

namespace common\tests\unit\models;

use common\models\Logs;

/**
 * Logs test
 */
class LogsTest extends \Codeception\Test\Unit
{

    public function testAdd()
    {
        $userId1 = rand(1, 9999);
        $userId2 = rand(1, 9999);
        $type1 = 'type1' . md5(time());
        $type2 = 'type2' . md5(time());
        $content1 = 'content';
        $content2 = json_encode(['test' => $content1]);

        Logs::add($userId1, 'Logs', $type1, $content1);
        Logs::add($userId2, 'Logs', $type2, $content2);

        $log1 = Logs::findOne(['user_id' => $userId1, 'model' => 'Logs', 'type' => $type1]);
        $log2 = Logs::findOne(['user_id' => $userId2, 'model' => 'Logs', 'type' => $type2]);

        expect('add type1 log', $log1 and $log1->content === 'content')->true();
        expect('add type2 log', $log2 and json_decode($log2->content)->test === 'content')->true();
    }
}
