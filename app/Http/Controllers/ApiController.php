<?php

namespace App\Http\Controllers;
use App\Model\Api;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //get store location
    public function getStoreLocations(){        
        $store_locations = Api::getStoreLocations();
        return response()->json([
            "data" => $store_locations,
            "status" => 200,
            "message" => "success"
        ]);
    }

    //get categories
    public function getCategories(){
        $categories     = Api::getCategories();
        $filters        = Api::getFilters();
        $filter_values  = Api::getFilterValues();
        return response()->json([
            "data" => array(
                "categories"    => $categories,
                "filters"       => $filters,
                "filter_values" => $filter_values
            ),
            "status" => 200,
            "message" => "success"
        ]);
    }


    //post get-quote appln
    public function addGetQuoteAppln(Request $request){
        $input = $request->all();
        if(!empty($input)){
            $quote_id = Api::addQuoteAppln($input);
            //print_r($request->file('quote_images')); exit;
            if(!empty($input['quote_images'])){
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

            return response()->json([   
                "data" => array(),             
                "status" => 200,
                "message" => "success"
            ]);

        }
        else{
            return response()->json([                
                "data" => array(),
                "status" => 500,
                "message" => "error"
            ]);
        }
        //return $input;

    }

    

    
}
