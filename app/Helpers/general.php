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
use Illuminate\Support\Str;

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

function uploadImage($image, $path)
{

    $imageName = explode('.', $image->getClientOriginalName());
    $name = str_replace(" ", "", $imageName[0] . '_' . \Carbon\Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension());
    $image = $image->move($path, $name);
    return $path . "/" . $name;
}

 function generateBcryptHash($userId)
{
    // Define a secret key
    $secretKey = '$2y$10$RIxZbN3vmqowhC5XaeBC4';
    // Concatenate the user ID with the secret key
    $valueToHash = $userId . $secretKey;
    // Generate the bcrypt hash
    $bcryptHash =str_replace(['.', '/'], '', $valueToHash);
    return $bcryptHash;
}

// Decrypt bcrypt hash to retrieve user ID
 function decryptHash($bcryptHash)
{
    // Define the secret key used during hash generation
    $secretKey = '$2y$10$RIxZbN3vmqowhC5XaeBC4';
    // Iterate over possible user IDs
    for ($i = 1; $i <= 1000; $i++) {
        // Concatenate the user ID with the secret key
        $valueToHash = $i . $secretKey;
        // Generate bcrypt hash using the same secret key and compare with provided hash
        if (password_verify($valueToHash, $bcryptHash)) {
            return $i; // Match found, return the user ID
        }
    }

    return null; // No match found
}


function deleteOldFiles($path){
    $directoryPath = public_path($path);
    if (File::isDirectory($directoryPath)) {
        File::deleteDirectory($directoryPath);
        // Directory deleted successfully
    } else {
        // Directory does not exist
    }
}
