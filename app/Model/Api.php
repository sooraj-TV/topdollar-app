<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\ResponseBuilder;

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
            'device_token' => $input['device_token'],
			'device_type' => $input['devicetype']
        );
        $count = DB::table('users')->where('device_id', $input['device_id'])->count(); // check the device_id already exist in DB
        if($count == 0){
            DB::table('users')->insert($data);
        } else {
            DB::table('users')->where('device_id', $input['device_id'])->update([
                'device_token' => $input['device_token'],
				'device_type' => $input['devicetype']
            ]);
        }     
        return true;
    }

    // Update device id and device token - for admin
    public static function updateDeviceDetails($input = array()){
        $data = array(
            'device_id' => $input['device_id'],
            'device_token' => $input['device_token']
        );
        DB::table('users')->where('id', $input['user_id'])->update($data);                
        return true;
    }

    public static function postChatAssociateAppln($input = array()){
        //$res = array();        
        $user_data = DB::table('users')->where('device_id', $input['device_id'])->first(); // get userdata by device_id
        $user_id = $user_data->id;

        isset($input['quote_id']) ? $quote_id = $input['quote_id'] : $quote_id = 0;
        
        $chat_data = array(            
            'user_id'           => $user_id,
            'name'              => $input['name'],
            'phone'             => $input['phone'],
            'store_location_id' => $input['store_location_id'],
            'question'          => $input['question'],
            'quote_id'          => $quote_id,
            'created_at'        => date("Y-m-d H:i:s")         
        );  
        //insert chat details in conversation table- init
        $chat_id = DB::table('chats')->insertGetId($chat_data);
        
        //update user table with below details
        DB::table('users')->where('id', $user_id)->update([
            'name'  => $input['name'],
            'phone' => $input['phone']
        ]);
        
        return $chat_id;

    }

    //get admin tokens
    public static function getUsers($type = ""){
        $data = DB::table('users')->where('device_token', '!=', '');
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
        if($input['user_id'] > 0){
            $admin_id = $input['user_id'];
        } else {
            $admin_id = self::getUserID($input['device_id']);
        }
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

            //send push notification to user when chat accepted            
            $user_data = DB::table('users')->where('id', $chat_data->user_id)->first();
            $device_type = $user_data->device_type;
            $device_token[] = $user_data->device_token;
            $notification = array(
                "title" => "Chat request accepted",
                "body"  => "Your chat request has been accepted",
                "badge" => 1        
            );
            $data = $notification;
            ResponseBuilder::sendPushNotification($device_token, $notification, $data, $device_type);     
            //exit;
        }
        return true;
    }

    //post chat messages
    public static function postChatMessagesAppln($input = array()){
        if($input['user_id'] > 0){
            $sender_id = $input['user_id'];
        } else {
            $sender_id = self::getUserID($input['device_id']);
        }        
        $chat_data = self::getChatDetails($input['chat_id']);
        //dd($chat_data);
        if($sender_id == $chat_data->user_id){
            $receiver_id = $chat_data->accepted_user_id;
        } else if($sender_id == $chat_data->accepted_user_id){
            $receiver_id = $chat_data->user_id;
        }
        
        $user_data = DB::table('users')->where('id', $receiver_id)->first();
        $device_token[] = $user_data->device_token;
        $device_type = $user_data->device_type;
        $badge_count = Api::updateBadgeCount($receiver_id);
        if(empty($badge_count)) $badge_count = 1;

        $msg_data = array(  
            "chat_id"       => $input['chat_id'],
            "sender_id"     => $sender_id,
            "receiver_id"   => $receiver_id,
            "message"       => $input['message'],
            "media_file"    => $input['media_file'],
            "created_at"    => date("Y-m-d H:i:s")            
        );

        DB::table('messages')->insert($msg_data);
        // ---------- send push notification  -----------
        $data = array(
            'chat_id' => $input['chat_id'],
            'frm'  => 'chat_message' 
        );
        $notification = array(
            "title" => "New message",
            "body"  => $input['message'],
            "badge" => $badge_count          
        );
        ResponseBuilder::sendPushNotification($device_token, $notification, $data, $device_type); 
        // ---------- send push notification  -----------
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

    //Get messages by chat id
    public static function getMessages($chat_id = ""){
        $url = str_replace('/index.php','',url(''));        
        if(!empty($chat_id)){
            $messages = DB::table('messages as m')
                        ->select("m.message", //DB::raw('CONCAT("'.$url.'/public/chat-images/",m.media_file) as chat_file'),
                            DB::raw('IF (m.media_file = "", "", CONCAT("'.$url.'/public/chat-images/",m.media_file)) as media_file'),
                            "sender.name as sender_name", "receiver.name as receiver_name", "sender.device_id as sender_device_id",
                            "receiver.device_id as receiver_device_id", "m.sender_id", "m.receiver_id", "m.created_at", "m.read_status"
                        )
                        ->leftJoin('users as sender', 'sender.id', '=', 'm.sender_id')
                        ->leftJoin('users as receiver', 'receiver.id', '=', 'm.receiver_id')
                        ->where('m.chat_id', $chat_id)
                        ->orderBy('m.id')
                        ->get(); 
            return $messages;
        } else {
            return "";
        }
    }

    //get chat list for admin
    public static function getChatLists($user_id = ""){        
        $chats = DB::table("chats as c")
                 ->select('c.id as chat_id', 'c.name as user_name', 'c.question', 'c.created_at', 'c.status');
        if(!empty($user_id)){
            $chats = $chats->where('c.accepted_user_id', $user_id);                                 
        } 
        $chats = $chats->orWhere('c.status', 'initiated')->OrderBy('c.created_at', 'desc')->get();
        return $chats;
    }

    //get last message
    public static function getLastMessage($chat_id = ""){
        $msg = DB::table('messages')
                ->where('chat_id', $chat_id)
                ->OrderBy('created_at', 'desc')
                ->first();
        return $msg;

    }

    //get chat list for admin
    public static function getChatRequests(){        
        $chats = DB::table("chats as c")
                 ->select('c.id as chat_id', 'c.name as user_name', 'c.question', 'c.created_at', 'c.status')
                 ->where('c.status', '!=', 'closed')
                 ->get();
        return $chats;
    }

    public static function updateMessageReadStatus($input = array()){
        $data = DB::table('messages')
                ->where('chat_id', $input['chat_id'])->update([
                    'read_status' => $input['status']
                ]);              
        return true;
    }

    //function static 

     //Function to get filters
     public static function getAdBanners(){
        $url = "https://topdollarjewelry.com/tdp-admin/";
        $data = DB::table('ad_banners as ab')
                ->select('ab.id',
                    DB::raw('IF (ab.banner_image = "", "", CONCAT("'.$url.'uploads/ads/",ab.banner_image)) as banner_image'),
                    'ab.external_link'
                )
                ->where('status', 1)
                ->get();
        return $data;
    }


    //update badge count and return count
    public static function updateBadgeCount($user_id = ""){
        if(!empty($device_token)){
            $data = DB::table('users')->where('id', $user_id)->get();
            //dd($device_token);
            $badge_count = $data->badge_count + 1;
            DB::table('users')->where('id', $user_id)->update([
                'badge_count' => $badge_count
            ]);
            return $badge_count;
        } else {
            return 1;
        }        
    }

    //reset badge count
    public static function resetBadgeCount($device_token = ""){
        if(!empty($device_token)){
            DB::table('users')->where('device_token', $device_token)->update([
                'badge_count' => 0
            ]);
        }

    }
}