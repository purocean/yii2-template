<?php
namespace console\controllers;

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Workerman\Lib\Timer;
use application\controllers\UserController;

/**
 * Workerman manager.
 */
class WorkermanController extends \yii\console\Controller
{
    /**
     * Run.
     */
    public function actionIndex()
    {
        global $argv;
        unset($argv[0]);
        $argv = array_values($argv);

        $io = new SocketIO(\Yii::$app->params['socketIOPort']);

        $io->on('connection', function ($socket) use ($io) {
            $timer = null;
            $socket->on('qrlogin', function ($nonce) use ($socket, $io, $timer) {
                $timer = Timer::add(2.5, function () use ($io, $nonce) {
                    $io->emit('qrlogin', UserController::Qrlogin($nonce));
                });

                $socket->on('disconnect', function ($nonce) use ($socket, $timer) {
                    is_int($timer) and Timer::del($timer);
                });
            });
        });

        Worker::runAll();

        return 0;
    }
}
