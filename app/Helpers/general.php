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

function calculateAgeFromNationalId($nationalId)
{
    if (substr($nationalId, 0, 1) == 2) {
        $array = str_split($nationalId);
        $dateOfBirth = "19" . $array[1] . $array[2] . "-" . $array[3] . $array[4] . "-" . $array[5] . $array[6];
        return calculateAgeFromDateOfBirth($dateOfBirth);
    } elseif (substr($nationalId, 0, 4) == 3000) {
        $array = str_split($nationalId);
        $dateOfBirth = "20" . $array[1] . $array[2] . "-" . $array[4] . $array[5] . "-" . $array[6] . $array[7];
        return calculateAgeFromDateOfBirth($dateOfBirth);
    } elseif (substr($nationalId, 0, 3) == 300) {
        $array = str_split($nationalId);
        $dateOfBirth = "20" . $array[1] . $array[2] . "-" . $array[3] . $array[4] . "-" . $array[5] . $array[6];
        return calculateAgeFromDateOfBirth($dateOfBirth);
    } else {
        $dateOfBirth = "2021-01-01";
        return calculateAgeFromDateOfBirth($dateOfBirth);
    }
}

function calculateAgeFromDateOfBirth($dateOfBirth)
{
    $birthdate = Carbon::parse($dateOfBirth);
    $currentDate = Carbon::now();

    $age = $birthdate->diffInYears($currentDate);

    return $age;
}

function generateBcryptHash($userId)
{
    // Define a secret key
    $secretKey = '$2y$10$RIxZbN3vmqowhC5XaeBC4';
    // Concatenate the user ID with the secret key
    $valueToHash = $userId . $secretKey;
    // Generate the bcrypt hash
    $bcryptHash = str_replace(['.', '/'], '', $valueToHash);
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


function deleteOldFiles($path)
{
    $directoryPath = public_path($path);
    if (File::isDirectory($directoryPath)) {
        File::deleteDirectory($directoryPath);
        // Directory deleted successfully
    } else {
        // Directory does not exist
    }
}


if (!function_exists('sendFcm')) {

    function send_fcm($tokens, $title, $message, $type = null, $data = [])
    {
        if (!empty($tokens)) {
            ob_start();
            $notification = [
                'data' => [
                    'type' => $type,
                    'title' => $title,
                    'body' => $message,
                    'data' => $data
                ],
                'notification' => [
                    'title' => $title,
                    'type' => $type,
                    'body' => $message,
                    'sound' => 'default',
                    'data' => $data
                ],
                "content_available" => true,
                "apns-priority" => "5",
                'registration_ids' => ["d8ocrNveSUGBSur9bip1m7:APA91bFG_H2EfgEHdDhFDUY4cGJ99dtEpmz9XEGMXSZPZ1Ks6b72tMtztYHffHthyvs0FdgRvb7zV_R61sqegv9QSTdXgvreM9n_61KC7aT_5cOzM9-fCtqnbmKP5HD5mNkiVB6IiAa8"],
                "android_channel_id"=> "Low Calories Channel",
                'priority' => 'high',
                "show_notification_android"=>"true",
                'sound' => 'default',
                "android"=> [
                    "priority"=> "high"
                ],
                'badge' => 1,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ];
            $headers = [
                'Authorization' => 'key=AAAAmm0zoMA:APA91bHJeMQiLObppn7UWBl2dx30MSx5GZi-pJz3RsmB6WEXyR29-h6wbLC37TiCvyK7HMrLtKme8YmHmtgpiFw03ViYZG7_wpqtMSrZ0oCbgIarcPl6KwTABXlIUk5RzjbyH_J7k0FL',
                'Content-Type' => 'application/json'
            ];
            $url = "https://fcm.googleapis.com/";
            $client = new \GuzzleHttp\Client([
                'base_uri' => $url,
            ]);

            ////// IOS /////
            $response = $client->post('fcm/send', [
                'debug' => fopen('php://stderr', 'w'),
                'body' => json_encode($notification),
                'headers' => $headers
            ]);
            ob_end_clean();
            return true;
        } else {
            return false;
        }
    }
}

