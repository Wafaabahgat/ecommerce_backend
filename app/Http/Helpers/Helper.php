<?php

namespace App\Http\Helpers;

use Illuminate\Http\Exceptions\HttpResponseException;

class Helper
{
    public static function sendError($message, $errors = [], $code = 401)
    {
        $response = ['success' => false, 'msg' => $message];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        throw new HttpResponseException(response()->json($response, $code));
    }

    public static function sendSuccess($message, $data = null, $code = 200)
    {
        $response = ['success' => true, 'msg' => $message, 'data' => $data];

        // dd($response, $code);
        return response()->json($response, $code);
    }
}
