<?php

namespace application\models;

use Yii;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $id
 * @property string $model
 * @property string $title
 * @property string $comment
 * @property string $name
 * @property string $path
 * @property string $hash
 * @property string $mime
 * @property integer $size
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path', 'hash', 'mime', 'size'], 'required'],
            [['path'], 'string'],
            [['size', 'status'], 'integer'],
            [['model', 'title', 'mime', 'comment', 'name'], 'string', 'max' => 255],
            [['hash'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => '模型',
            'title' => '标题',
            'comment' => '备注',
            'name' => '文件名',
            'path' => '文件路径',
            'hash' => '文件哈希',
            'mime' => '文件类型',
            'size' => '文件尺寸',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function encodeName($name)
    {
        return str_replace(['\\', '/', ':', '*', '？', '"', '<', '>', '|'], '-', $name);
    }

    public static function getRealPath($path)
    {
        return Yii::getAlias('@storage/application/uploads/') . trim($path, '/');
    }

    public static function upload($modelName, $file, $title = '', $comment = '')
    {
        $fileName = Files::encodeName($file->baseName . '.' . $file->extension);
        $realName = md5($fileName) . '_' . time();
        $filePath = $realName;
        $realPath = Files::getRealPath($filePath);

        if ($file->hasError or !$file->saveAs($realPath)) {
            throw new \Exception('保存文件失败');
        }

        $model = new self();
        $model->model = $modelName;
        $model->title = $title;
        $model->comment = $comment;
        $model->name = $fileName;
        $model->path = $filePath;
        $model->hash = md5(file_get_contents($realPath));
        $model->mime = $file->type;
        $model->size = $file->size;

        if ($model->save()) {
            // 生成缩略图
            if (stripos($model->mime, 'image/') === 0) {
                Yii::$app->image->load($realPath)
                    ->resize(200, 200)
                    ->save(Yii::getAlias('@storage/application/uploads/thumbs/') . $model->path);
            }

            return $model;
        } else {
            @unlink($filePath);
            return false;
        }
    }
}
