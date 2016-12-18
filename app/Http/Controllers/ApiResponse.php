<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

trait ApiResponse
{
    protected function response($status, $data = [])
    {
        return response()->json($data, $status);
    }

    protected function modelNotFound($message = null)
    {
        $message = $message ?: 'The given id not found in our database.';

        return $this->notFound([
            'message' => $message,
        ]);
    }

    protected function modelDeleted($message = null)
    {
        $message = $message ?: 'The given id has been deleted from our database.';

        return $this->ok([
            'message' => $message,
        ]);
    }

    protected function ok($data = [])
    {
        return response()->json($data, Response::HTTP_OK);
    }

    protected function created($data = [])
    {
        return response()->json($data, Response::HTTP_CREATED);
    }

    protected function badRequest($data = [])
    {
        return response()->json($data, Response::HTTP_BAD_REQUEST);
    }

    protected function notFound($data = [])
    {
        return response()->json($data, Response::HTTP_NOT_FOUND);
    }

    protected function conflict($data = []) {
        return response()->json($data, Response::HTTP_CONFLICT);
    }

    protected function serverError($data = []) {
        return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
