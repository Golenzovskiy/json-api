<?php
/**
 * @author Golenzovskiy Stanislav <golenzovskiy@gmail.com>
 * @copyright Copyright (c) 2017, json-api
 */

namespace App\Helpers\Api;

class Helper
{
    public static function checkRequireFields($model, array $request, array $requireFields)
    {
        foreach ($requireFields as $requireField) {
            if (!isset($request[$requireField])) {
                $model->errors[] = "Не получено поле $requireField";
            }
        }
        return (empty($model->errors)) ? true : false;
    }

    public static function isScoreCorrect($model, $score)
    {
        if (!is_numeric($score) || $score < 1 || $score > 5) {
            $model->errors[] = "Установлено недопустимое значение.";
            return false;
        }
        return true;
    }
}
