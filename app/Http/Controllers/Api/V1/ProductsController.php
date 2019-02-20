<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Response;
use Auth;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

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

        return response()->json(['success' => true,'message'=>'Product is created.'], 200);
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if($product){
            return response()->json(['product'=>$product],200);
        }else{
            return response()->json(['message'=>'Something went wrong'],500);
        }
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
        $product = Product::find($id);
        if($product){
            $file = $request->file('product_image');

            $validator = Validator::make($request->all(), [
                'product_name' => 'required',
                'product_image' => 'required', // max 10000kb
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
      
            if($file){
                $ImageName = time().'.'.$file->getClientOriginalExtension();
                //Move Uploaded File
                $file->move($this->uploadDirectory,$ImageName);   
            }else{
                $ImageName = $request->product_image;
            }
            $product->product_name  = $request->product_name;
            $product->product_image = $ImageName;
            $product->regular_price = $request->regular_price;
            $product->sale_price    = $request->sale_price;
            $product->stock         = $request->stock;
            $product->product_sku   = $request->product_sku;
            $product->user_id       = Auth::user()->id;

            $product->save();

            return response()->json(['success' => true,'message'=>'Product is updated.'], 200);

        }else{
            return response()->json(['message'=>'Something went wrong'],500);
        }
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

    /**
     * Load the image through the URL
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function loadImage($fileName){
        $path = public_path().'/'.$this->uploadDirectory.$fileName;
        return Response::download($path);        
    }

    public function importExcel(Request $request){
        
        $file = $request->file('excel_file');

        $validator = Validator::make($request->all(), [
            'excel_file' => 'mimetypes:application/csv,application/excel,application/vnd.ms-excel,application/vnd.msexcel,text/csv,text/anytext,text/plain,text/x-c,text/comma-separated-values,inode/x-empty,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|required', // max 10000kb
        ]);

        if ($validator->fails()) {
            // Redirect or return json to frontend with a helpful message to inform the user 
            // that the provided file was not an adequate type
            return response()->json(['error' => $validator->errors()->getMessages()], 400);
        }
        if($file){
            Excel::import(new ProductsImport, request()->file('excel_file'));
            return response()->json(['message'=> 'Data successfully imported'],200);
        }else{
            return response()->json(['message'=> 'file not found'],400);
        }

    }

    public function exportExcel(){
        // Store on default disk
        return Excel::download(new ProductsExport, 'products.xlsx');    
    }
}
