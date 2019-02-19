<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Response;
use Auth;

class ProductsController extends Controller
{
    protected $uploadDirectory = 'products/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Product::orderBy('id')->paginate(10);
        return response()->json($data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('product_image');

        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'product_image' => 'mimes:jpeg,jpg,png,gif|required|max:10000', // max 10000kb
            'regular_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'sale_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'stock' => 'required|numeric',
            'product_sku' => 'required',
        ]);

        if ($validator->fails()) {
            // Redirect or return json to frontend with a helpful message to inform the user 
            // that the provided file was not an adequate type
            return response()->json(['error' => $validator->errors()->getMessages()], 400);
        }
      
        $ImageName = time().'.'.$file->getClientOriginalExtension();
        //Move Uploaded File
        $file->move($this->uploadDirectory,$ImageName);
        
        $product = new Product;

        $product->product_name  = $request->product_name;
        $product->product_image = $ImageName;
        $product->regular_price = $request->regular_price;
        $product->sale_price    = $request->sale_price;
        $product->stock         = $request->stock;
        $product->product_sku   = $request->product_sku;
        $product->user_id       = Auth::user()->id;

        $product->save();

        return response()->json(['success' => true,'message'=>'Product is created.'], 400);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadImage($fileName){
        $path = public_path().'/'.$this->uploadDirectory.$fileName;
        return Response::download($path);        
    }
}
