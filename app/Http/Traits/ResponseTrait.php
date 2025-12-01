<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    public function successResponse($data = [], $message = null, $statusCode = 200)
    {
        $response = [
            'status' => 'success',
            'statuscode' => $statusCode,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }
    public function errorResponse($message = null,  $statusCode = 500)
    {
        $response = [
            'status' => 'failure',
            'statuscode' => $statusCode,
            'message' => $message,
        ];



        return response()->json($response, $statusCode);
    }
}
