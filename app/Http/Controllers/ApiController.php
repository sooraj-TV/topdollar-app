<?php

namespace App\Http\Controllers;
use App\Model\Api;
use Illuminate\Http\Request;
use App\Http\Helper\ResponseBuilder;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // return response()->json([
        //     "data" => $store_locations,
        //     "status" => 200,
        //     "message" => "success"
        // ]);
    }

    //get store location
    public function getStoreLocations(){        
        $store_locations = Api::getStoreLocations();        
        return ResponseBuilder::result(200, 'success', $store_locations);        
    }

    //get categories
    public function getCategories(){
        $categories     = Api::getCategories();
        $filters        = Api::getFilters();
        $filter_values  = Api::getFilterValues();
        $data = array(
            "categories"    => $categories,
            "filters"       => $filters,
            "filter_values" => $filter_values
        );
        return ResponseBuilder::result(200, 'success', $data);        
    }

    //post get-quote appln
    public function addGetQuoteAppln(Request $request){
        $input = $request->all();
        if(!empty($input)){
            $quote_id = Api::addQuoteAppln($input);
            if($quote_id > 0){
                if(!empty($input['quote_images'])){ // optional
                    foreach($request->file('quote_images') as $image)
                    {
                        $name = $image->getClientOriginalName();
                        $image->move('public/quote-images/', $name);  
                        $quote_image_data = array(
                            'quote_id' => $quote_id,
                            'image_path' => $name
                        );
                        Api::addQuoteImageAppln($quote_image_data);
                    }         
                }
                //insert quote data to chat table and return chat_id
                $chat_data = array(
                    'name'              => $input['name'],
                    'phone'             => $input['phone'],
                    'store_location_id' => $input['store_location_id'],
                    'question'          => $input['description'],
                    'device_id'         => $input['device_id'],
                    'quote_id'          => $quote_id
                );
                $chat_id = Api::postChatAssociateAppln($chat_data);
                $res_data = array(
                    'chat_id' => $chat_id
                );
                return ResponseBuilder::result(200, 'success', $res_data); // return success message if the contents stores in DB    
            }
            else{
                return ResponseBuilder::result(500, 'error');    
            }            

        }
        else{ // if input data is empty            
            return ResponseBuilder::result(204, 'no_input_content');        
        }
        //return $input;
    }

    //register device API
    public function postRegisterDevice(Request $request){
        $input = $request->all();
        $data = Api::registerDeviceAppln($input);
        if($data){
            return ResponseBuilder::result(200, 'success');        
        } else {
            return ResponseBuilder::result(500, 'error');        
        }
    }

    public function postChatAssociate(Request $request){
        $input = $request->all();
        $chat_id = Api::postChatAssociateAppln($input);
        if($chat_id > 0){
            //send push notification to all admins
            $admins = Api::getUsers('admin'); // get admin users            
            foreach($admins as $admin){
                $admin_tokens[] = $admin->device_token;
            }
            //dd($admin_tokens);
            $message = "Chat request from a user: ".$input['name'];            
            $data = array(
                'chat_id' => $chat_id
            );
            $notification = array(
                "title" => "Chat Request",
                "body"  => $message            
            );
            ResponseBuilder::sendPushNotification($admin_tokens, $notification, $data);                        
            return ResponseBuilder::result(200, 'success', $data);   
        }
        else{
            return ResponseBuilder::result(500, 'error');    
        }
    }

    //Get chat details
    public function getChatDetails($chat_id = ''){
        //$input = $request->all();        
        //echo "Chatid: ".$chat_id; exit;
        if(!empty($chat_id)){
            $chat_details = Api::getChatDetails($chat_id);  
            //dd($chat_details);               
            if(!empty($chat_details)){                
                return ResponseBuilder::result(200, 'success', $chat_details); 
            } else{
                return ResponseBuilder::result(404, 'record_not_found'); 
            }
              
        } else {
            return ResponseBuilder::result(500, 'empty_chatid');    
        }

    }

    //accept chat appln - by admin
    public function acceptChatAppln(Request $request){
        $input = $request->all();
        $res = Api::acceptChatAppln($input);
        $data = array(
            'status' => $input['status']
        );
        if($res){
            return ResponseBuilder::result(200, 'success', $data); 
        } else {
            return ResponseBuilder::result(500, 'error');    
        }
    }

    public function postChatMessages(Request $request){
        $input = $request->all();
        if(!empty($input)){
            $chat_details = Api::getChatDetails($input['chat_id']);
            //dd($chat_details);
            if(empty($chat_details)){
                return ResponseBuilder::result(404,'chat_not_found');
            }
            if($chat_details->chat_status == "accepted"){
                $input['media_file'] = "";
                $chat_file_url = "";
                if(!empty($input['chat_file'])){ // optional - chat image 
                    //print_r($input['chat_file']);
                    $image = $request->file('chat_file');
                    $name = $image->getClientOriginalName();
                    $image->move('public/chat-images/', $name);  
                    $input['media_file'] = $name;        
                    $chat_file_url = url('public/chat-images/'.$name);
                    $chat_file_url = str_replace('/index.php','',$chat_file_url);
                }            
                $res = Api::postChatMessagesAppln($input);
                
                $chat_data = array(
                    'message' => $input['message'],
                    'chat_media_url' => $chat_file_url
                );
                if($res){
                    return ResponseBuilder::result(200, 'success',$chat_data); 
                } else {
                    return ResponseBuilder::result(500, 'error'); 
                }
            } else { // if chat is not accepted
                return ResponseBuilder::result(500, 'error', array("message"=> "Sorry! This chat is not accepted yet."));
            }
        } else {
            return ResponseBuilder::result(204, 'no_input_content');
        }

    }

    public function getChatMessages($chat_id = ""){
        
        if(!empty($chat_id)){
            $msg_data = array(
                "chat_data" => Api::getChatDetails($chat_id),
                "messages" => Api::getMessages($chat_id)   
            );         
            //dd($msg_data);    
            if(!empty($msg_data)){                
                return ResponseBuilder::result(200, 'success', $msg_data); 
            } else{
                return ResponseBuilder::result(404, 'record_not_found'); 
            }
              
        } else {
            return ResponseBuilder::result(500, 'empty_chatid');    
        }
    }

    //Get chat list for admin
    public function getChatLists($user_id = ""){
        if(!empty($user_id)){
            $chat_list = Api::getChatLists($user_id);         
            //dd($chat_list);    
            if(!empty($chat_list)){                
                return ResponseBuilder::result(200, 'success', $chat_list); 
            } else{
                return ResponseBuilder::result(404, 'record_not_found'); 
            }
              
        } else {
            return ResponseBuilder::result(500, 'empty_chatid');    
        }
    }

    //Get all chat requests for admin
    public function getChatRequests(){    
        $chat_list = Api::getChatRequests();          
        if(!empty($chat_list)){                
            return ResponseBuilder::result(200, 'success', $chat_list); 
        } else {
            return ResponseBuilder::result(404, 'record_not_found'); 
        }
                      
    }    



    //********************* TEST CONTROLLERS ************************/
    //test middleware auth check
    public function authCheck(){
        echo "Hello! Success";        
    }

    //send test push notification
    public function sendPushNotification_TEST(Request $request){
        $input = $request->all();
        $device_tokens[] = $input['device_token'];    
        $notification = array(
            "title" => "Hello!",
            "body"  => $input['message']            
        );
        $data = $notification;
        return ResponseBuilder::sendPushNotificationTEST($device_tokens, $notification, $data);        

    }

    

    
}
