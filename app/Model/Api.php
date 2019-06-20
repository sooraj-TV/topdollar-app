<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Api extends Model{

    //Function to get store location
    public static function getStoreLocations(){
        $data = DB::table('store_locations')->where('status', 1)->get();
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
}