<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;

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
        return view('product.index');
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('products.show');
    }

    public function edit($id)
    {
        return view('products.edit');
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
