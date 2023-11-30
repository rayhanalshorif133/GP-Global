<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class ServiceController extends Controller
{


    public function index()
    {
        if (request()->ajax()) {
            $query = Service::orderBy('id', 'desc')->get();
            return DataTables::of($query)
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('service.index');
    }

    // create
    public function store(Request $request)
    {
        $isValidator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'validity' => 'required',
            'keyword' => 'required|unique:services',
        ]);



        if ($isValidator->fails()) {
            flash()->addError($isValidator->errors()->first());
            return redirect()->route('service.index');
        }

        try {
            $service = new Service();
            $service->name = $request->name;
            $service->keyword = $request->keyword;
            $service->amount = $request->amount;
            $service->type = $request->type;
            $service->validity = $request->validity;
            $service->redirect_url = $request->redirect_url;
            $api_url = url('api/consent/prepare') . "/" . $request->validity . "/" . $request->keyword ."/{msisdn}";
            $service->api_url = $api_url;
            $service->description = $request->description;
            $service->save();
            flash()->addSuccess('Service created successfully!');
            return redirect()->route('service.index');
        } catch (\Throwable $th) {
            flash()->addError($th->getMessage());
            return redirect()->route('service.index');
        }
    }

    // edit
    public function show($id)
    {
        $service = Service::find($id);
        return $this->respondWithSuccess('Service fetched successfully!', $service);

    }

    public function edit($id)
    {
        $service = Service::find($id);
        return $this->respondWithSuccess('Service fetched successfully!', $service);
    }


    public function getServiceKey()
    {
        $key = $this->generateRandomString(6);
        $service = Service::where('service_key', $key)->first();
        if ($service) {
            $this->getServiceKey();
        }
        return $key;
    }


    // update

    public function update(Request $request, $id)
    {
        $isValidator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'redirect_url' => 'required',
            'keyword' => 'required|unique:services,keyword,' . $id . ',id',
            'validity' => 'required',
        ]);



        if ($isValidator->fails()) {
            flash()->addError($isValidator->errors()->first());
            return redirect()->route('service.index');
        }



        try {


            $service = Service::find($id);
            $service->name = $request->name;
            $service->type = $request->type;
            $service->keyword = $request->keyword;
            $service->description = $request->description;
            $service->amount = $request->amount;
            $service->validity = $request->validity;
            $api_url = url('api/consent/prepare') . "/" . $request->validity . "/" . $request->keyword ."/{msisdn}";
            $service->api_url = $api_url;
            $service->redirect_url = $request->redirect_url;
            $service->save();

            flash()->addSuccess('Service updated successfully!');
        } catch (\Throwable $th) {
            flash()->addError('Something went wrong!');
        }
        return redirect()->route('service.index');
    }


    // delete

    public function destroy($id)
    {
        try {
            $service = Service::find($id);
            $service->delete();
            flash()->addSuccess('Service deleted successfully!');
            return $this->respondWithSuccess('Service deleted successfully!');
        } catch (\Throwable $th) {
            flash()->addError('Something went wrong!');
            return $this->respondWithError('Something went wrong!');
        }
    }


    public function generateRandomString($length = 25)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    // serviceSubscription
    public function serviceSubscription(Request $request){

        if($request->phone_number == ""){
            flash()->addError('Please input a phone number!');
        }

        $service = Service::find($request->service_id);
        if($service){
            $url = url('api/consent/prepare') . "/" . $service->validity . "/P1/" . $request->phone_number;
            return redirect($url);
        }else{
            flash()->addError('Service is not found!');
        }
        return redirect()->back();

    }

    // serviceRefund
    public function serviceRefund(Request $request){
        $url = url('refund') . "/" . $request->acr_key;
        return redirect($url);
    }
}
