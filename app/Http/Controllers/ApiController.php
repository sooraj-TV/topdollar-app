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

    public function sendPushNotification(Request $request){
        $input = $request->all();
        if($input['device'] == "android"){
            return ResponseBuilder::sendPushNotification_android($input['device_id'], $input['message']);
        }
       


    }
    

    
}
