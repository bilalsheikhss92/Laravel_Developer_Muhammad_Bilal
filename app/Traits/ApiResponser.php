<?php

namespace App\Traits;

/*
|--------------------------------------------------------------------------
| API Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/


trait ApiResponser
{
    protected function success(string $message = null, $data = null, int $code = 200)
    {
        return $this->response($message, $data, 'Success', $code);
    }

    protected function error(string $message = null, $data = null, int $code = 404)
    {
        return $this->response($message, $data, 'Error', $code);
    }

    public function response($message = null, $data = null, $status = null, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], 200);
    }
}
