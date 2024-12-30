<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected function response($status = 0, $data = [], $message = "", $meta = [])
    {
        $response = [
            'code' => $status,
            'data' => $data,
            'message' => $message
        ];

        if (!empty($meta)) {
            $response["meta"] = $meta;
        }
        return response()->json($response, $status);
    }

    protected function getMetaData($modelInstance): array
    {
        return [
            "current_page" => $modelInstance->currentPage(),
            "per_page" => $modelInstance->perPage(),
            "last_page" => $modelInstance->lastPage(),
            "total" => $modelInstance->total(),
            "current_page_record" => $modelInstance->count(),
        ];
    }
}
