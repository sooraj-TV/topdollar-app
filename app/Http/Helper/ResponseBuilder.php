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
}