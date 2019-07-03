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
                return ResponseBuilder::result(200, 'success'); // return success message if the contents stores in DB    
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
            $push_data = array (
                "message" => $message,
                "chat_id" => $chat_id
            );
            ResponseBuilder::sendPushNotification($admin_tokens, $push_data);
            
            $data = array(
                'chat_id' => $chat_id
            );
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
            if(sizeof($chat_details)){                
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
            $input['media_file'] = "";
            if(!empty($input['media_file'])){ // optional - chat image 
                $image = $request->file('media_file');
                $name = $image->getClientOriginalName();
                if($image->move('public/chat-images/', $name)){
                    $input['media_file'] = $name;
                }        
            }            
            $res = Api::postChatMessagesAppln($input);
            if($res){
                return ResponseBuilder::result(200, 'success'); 
            } else {
                return ResponseBuilder::result(500, 'error'); 
            }
        } else {
            return ResponseBuilder::result(204, 'no_input_content');
        }

    }

    //send test push notification
    public function sendPushNotification_TEST(Request $request){
        $input = $request->all();
        $device_tokens [] = $input['device_token'];    
        $push_data = array(
            'message' => $input['message']
        ); 
        return ResponseBuilder::sendPushNotification($device_tokens, $push_data);        

    }

    

    
}
