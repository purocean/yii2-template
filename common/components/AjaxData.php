<?php

namespace common\components;

use yii\base\Component;

/**
* Ajax Data
*/
class AjaxData extends Component
{

    public static function build($status, $message = null, $data = null, $errors = null, $code = 0)
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'code' => $code,
        ];
    }
}
