<?php
namespace application\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\filters\VerbFilter;
use application\models\Files;
use common\components\AjaxData;

/**
 * Files controller
 */
class FilesController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'upload' => ['post'],
                    ],
                ]
            ]
        );
    }

    public function actionIndex($id, $thumb = '0')
    {
        if (!$file = Files::findOne($id)) {
            return '文件不存在';
        }

        if ($thumb == '1') {
            return Yii::$app->response->sendFile(Files::getRealPath('thumbs/' . $file->path), 'thumb_'.$file->name);
        } else {
            return Yii::$app->response->sendFile(Files::getRealPath($file->path), $file->name);
        }
    }

    public function actionUpload($model = 'upload', $title = '', $comment = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($file = UploadedFile::getInstanceByName('file')) {
            try {
                if ($fileInfo = Files::upload($model, $file, $title, $comment)) {
                    return AjaxData::build('ok', '上传成功', $fileInfo);
                } else {
                    return AjaxData::build('error', '写入数据库失败');
                }
            } catch (\Exception $e) {
                return AjaxData::build('error', '错误：'.$e->getMessage(), null, $e);
            }
        }

        return AjaxData::build('error', '上传失败');
    }
}
