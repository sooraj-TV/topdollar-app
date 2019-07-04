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

    //get chat details from chat-id
    public static function getChatDetails($chat_id = ""){
        $data = DB::table("chats as c")
                ->select('c.id as chat_id', 'c.name as user_name', 'c.phone as phone', 
                'sl.store_name as store_location', 'c.question as question', 'c.created_at', 
                'c.status as chat_status', 'c.user_id', 'c.accepted_user_id')
                ->where('c.id', $chat_id)
                ->join('store_locations as sl', 'sl.id', '=', 'c.store_location_id')                
                ->first();
        return $data;
    }

    //accept/reject chat by admin
    public static function acceptChatAppln($input = array()){
        $admin_id = self::getUserID($input['device_id']);
        $data = array(
            'accepted_user_id' => $admin_id,
            'status'    => $input['status']
        );

        DB::table('chats')->where('id', $input['chat_id'])->update($data);

        if($input['status'] == "accepted"){
            $chat_data = DB::table('chats')->where('id', $input['chat_id'])->first();
            $msg_data = array(
                'chat_id'       => $input['chat_id'],
                'sender_id'     => $chat_data->user_id,
                'receiver_id'   => $admin_id,
                'message'       => $chat_data->question,
                'created_at'    => date("Y-m-d H:i:s")
            );
            DB::table('messages')->insert($msg_data);
        }
        return true;
    }

    //post chat messages
    public static function postChatMessagesAppln($input = array()){
        $sender_id = self::getUserID($input['device_id']);
        $chat_data = self::getChatDetails($input['chat_id']);
        //dd($chat_data);
        if($sender_id == $chat_data->user_id){
            $receiver_id = $chat_data->accepted_user_id;
        } else if($sender_id == $chat_data->accepted_user_id){
            $receiver_id = $chat_data->user_id;
        }
        $msg_data = array(  
            "chat_id"       => $input['chat_id'],
            "sender_id"     => $sender_id,
            "receiver_id"   => $receiver_id,
            "message"       => $input['message'],
            "media_file"    => $input['media_file'],
            "created_at"    => date("Y-m-d H:i:s")            
        );

        DB::table('messages')->insert($msg_data);
        return true;

    }

    //get user_id from device_id
    public static function getUserID($device_id = ""){
        if(!empty($device_id)){
            $user_data = DB::table('users')->where('device_id', $device_id)->first(); // get userdata by device_id
            if(!empty($user_data)){
                return $user_data->id;
            } else {
                return "";
            }
        } else {
            return "";
        }
    }


    public static function getMessages($chat_id = ""){
        $url = str_replace('/index.php','',url(''));        
        if(!empty($chat_id)){
            $messages = DB::table('messages as m')
                        ->select("m.message", //DB::raw('CONCAT("'.$url.'/public/chat-images/",m.media_file) as chat_file'),
                            DB::raw('IF (m.media_file = "", "", CONCAT("'.$url.'/public/chat-images/",m.media_file)) as media_file'),
                            "sender.name as sender_name", "receiver.name as receiver_name", "sender.device_id as sender_device_id",
                            "receiver.device_id as receiver_device_id"
                        )
                        ->leftJoin('users as sender', 'sender.id', '=', 'm.sender_id')
                        ->leftJoin('users as receiver', 'receiver.id', '=', 'm.receiver_id')
                        ->where('m.chat_id', $chat_id)
                        ->get(); 
            return $messages;
        } else {
            return "";
        }
    }
}