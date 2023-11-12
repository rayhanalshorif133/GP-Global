<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Product::orderBy('id', 'desc')
                ->with('service')->get();
            return DataTables::of($query)
                ->rawColumns(['action'])
                ->toJson();
        }
        $services = Service::all();
        return view('product.index', compact('services'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service_id' => 'required',
            'product_key' => 'required|unique:products',
        ]);

        if($validator->fails()){
            flash()->addError($validator->errors()->first());
        }else{

            $product = new Product();
            $product->name = $request->name;
            $product->service_id = $request->service_id;
            $product->product_key = $request->product_key;
            $product->description = $request->description ? $request->description : "No description";
            $product->save();
            flash()->addSuccess("Successfully create a product");
        }
        return redirect()->back();
    }
    

    public function edit($id)
    {
        $product = Product::find($id);
        return $this->respondWithSuccess('Product fetched successfully!', $product);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service_id' => 'required',
            'product_key' => 'required|unique:products,product_key,' . $id,
        ]);

        if($validator->fails()){
            flash()->addError($validator->errors()->first());
        }else{

            $product = Product::select()->where('id',$id)->first();
            $product->name = $request->name;
            $product->service_id = $request->service_id;
            $product->product_key = $request->product_key;
            $product->description = $request->description ? $request->description : "No description";
            $product->save();
            flash()->addSuccess("Successfully update this product");
        }
        return redirect()->route('product.index');
    }


    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            flash()->addSuccess('Product deleted successfully!');
            return $this->respondWithSuccess('Product deleted successfully!');
        } catch (\Throwable $th) {
            flash()->addError('Something went wrong!');
            return $this->respondWithError('Something went wrong!');
        }
    }
}
