<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Api extends Model{

    //Function to get store location
    public static function getStoreLocations(){
        $data = DB::table('store_locations')->where('status', 1)->get();
        if(empty($data)){
            $data = array();
        }
        return $data;
    }

    //Function to get categories
    public static function getCategories(){
        $data = DB::table('categories')->where('status', 1)->get();
        return $data;
    }

    //Function to get filters
    public static function getFilters(){
        $data = DB::table('filters')->get();
        return $data;
    }

    //Function to get filters
    public static function getFilterValues(){
        $data = DB::table('filter_values')->get();
        return $data;
    }

    //insert quote info 
    public static function addQuoteAppln($input = array()){
        $qData = array(
            'name'          => $input['name'],
            'phone'         => $input['phone'],
            'store_location'=> $input['store_name'],
            'category_id'   => $input['category_id'],
            //'category_name' => $input['category_name'],
            'model_number'  => $input['model_number'],
            'brand'         => $input['brand'],
            'description'   => $input['description'],
            'device_token'  => $input['device_id'],
            'created_at'    => date("Y-m-d H:i:s")
        );
        //quote_images[]
        $quote_id = DB::table('quotes')->insertGetId($qData);

        return $quote_id;
    }

    // insert quote image data
    public static function addQuoteImageAppln($idata = array()){
        DB::table('quote_images')->insert($idata);
        return true;
    }

    public static function registerDeviceAppln($input = array()){
        $data = array(
            'device_id' => $input['device_id'],
            'device_token' => $input['device_token']
        );
        $count = DB::table('users')->where('device_id', $input['device_id'])->count(); // check the device_id already exist in DB
        if($count == 0){
            DB::table('users')->insert($data);
        }        
        return true;
    }

    public static function postChatAssociateAppln($input = array()){
        //$res = array();
        $user_data = DB::table('users')->where('device_id', $input['device_id'])->first(); // get userdata by device_id
        $user_id = $user_data->id;
        
        $chat_data = array(            
            'user_id'           => $user_id,
            'name'              => $input['name'],
            'phone'             => $input['phone'],
            'store_location_id' => $input['store_location_id'],
            'question'          => $input['question'],
            'created_at'        => date("Y-m-d H:i:s")         
        );  
        //insert chat details in conversation table- init
        $chat_id = DB::table('chats')->insertGetId($chat_data);

        // $msg_data = array(
        // );
        
        //update user table with below details
        DB::table('users')->where('id', $user_id)->update([
            'name'  => $input['name'],
            'phone' => $input['phone']
        ]);
        
        return $chat_id;

    }

    //get admin tokens
    public static function getUsers($type = ""){
        $data = DB::table('users')->where('device_id', '!=', '');
        if(!empty($type)){
            $data = $data->where('type', $type);
        }
        $data = $data->get();
        return $data;
        
    }
}