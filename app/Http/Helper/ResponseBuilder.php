<?php
namespace App\Http\Helper;

class ResponseBuilder {
    public static function result($status = "", $message = "", $data = array()){
        return response()->json([   
            "data" => $data,             
            "status" => $status,
            "message" => $message
        ],$status); 
    }

    //send push notification
    public static function sendPushNotification($registraion_ids = array(), $message = ""){

        //API URL of FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
    
        /*api_key available in:
        Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
        $api_key = env('FIREBASE_KEY');
                    
        $fields = array (
            'registration_ids' => $registraion_ids,
            'data' => array (
                    "message" => $message
            )
        );
    
        //header includes Content type and api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$api_key
        );
                    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}