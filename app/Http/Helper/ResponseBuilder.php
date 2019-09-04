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
    public static function sendPushNotification($registration_ids = array(), $notification = array(), $data = array(), $device = ""){

        //API URL of FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
    
        /*api_key available in:
        Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
        $api_key = env('FIREBASE_KEY');
        $notif = array(
            "sound" => 'default', 
            "badge" => '1',
            "content-available" => "1"    
        );
        $notification = array_merge($notification, $notif);
        if($device == "android"){
            $data = array_merge($data, $notification);
            $fields = array (
                "registration_ids" => $registration_ids,
                "priority"      => "high",
                //"notification"  => $notification,
                "data"          => $data				
            );
        } else {
            $fields = array (
                "registration_ids" => $registration_ids,
                "priority"      => "high",
                "notification"  => $notification,
                "data"          => $data				
            );
        }

        //print_r($fields); exit;
    
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

    //send push notification - TEST
    public static function sendPushNotificationTEST($registration_ids = array(), $notification = array(), $data = array(), $device = ""){

        //API URL of FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
    
        /*api_key available in:
        Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
        $api_key = env('FIREBASE_KEY');          
        
        $notif = array(
            "sound" => 'default', 
            "content-available" => "1"    
        );
        $notification = array_merge($notification, $notif);
        if($device == "android"){
            $data = array_merge($data, $notification);
            $fields = array (
                "registration_ids" => $registration_ids,
                "priority"      => "high",
                //"notification"  => $notification,
                "data"          => $data				
            );
        } else {
            $fields = array (
                "registration_ids" => $registration_ids,
                "priority"      => "high",
                "notification"  => $notification,
                "data"          => $data				
            );
        }
        
    
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
