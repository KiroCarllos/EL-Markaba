<?php

use App\Models\CustomPrices;
use App\Models\FortyDeliveryDay;
use App\Models\TwentyDeliveryDay;
use App\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

if (!function_exists('api_response')) {
    function api_response($status, $message, $data = null, $status_code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

//        $pagination = api_model_set_pagenation($data);
//        if ($pagination) $response['pagination'] = $pagination;
        return response()->json($response, $status_code);
    }
}
