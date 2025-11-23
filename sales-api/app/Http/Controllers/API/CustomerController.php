<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $customers = null;
        $pageSize = $request->pageSize;

        if($search){
            $customers = Customer::where('name', 'like', '%'.$search.'%')
                            ->orderBy('id')
                            ->paginate($pageSize);            
        }else{
            $customers = Customer::orderBy('id')
                            ->paginate($pageSize);
        }



        return response()->json(
            [   'code'  => 200,
                'total' => $customers->total(),
                'customers' => CustomerCollection::make($customers)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                  'name'        => 'required',
                  // 'surname'        => 'required',
                  'num_identificador' => 'required|unique:customers',
                  'email'       => 'required|email|unique:customers',
                  'phone'       => 'required',
                  'address'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $customer = Customer::create($request->all());
        return response()->json(['code' => 200,
                                 'message'=> 'customer created',
                                 'customer'=> $customer]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::where('state', 1)
                            ->where('id','=',$id)->first();

        if(!$customer){
            $data = ['code'=> 404,
                     'message' => 'Customer not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => CustomerResource::make($customer )];
        }
        return response()->json(['code' => $data['code'], 
                                 'Customer' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */              
    public function update(Request $request, string $id)
    {   
        $validator = Validator::make($request->all(), [
                  'name'        => 'required',
                  // 'surname'        => 'required',
                  'num_identificador' => ['required', Rule::unique('customers')->ignore($id),],
                  'email'       => ['required','email', Rule::unique('customers')->ignore($id),],
                  'phone'       => 'required',
                  'address'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $customer = Customer::find($id);
        if(! $customer){
            $data = ['code'=> 404,
                     'message' => 'Customer not found'];
        }
        $customer->update($request->all());
        $data = ['code'=> 200, 'message' => 'Customer updated'];

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::where('id','=',$id)->first();
                
        if(!$customer){
            $data = ['code'=> 404,
                     'message' => 'Customer not found'];
        }else{
            if($customer->state==1){
                $customer->state = 0;
                $message='Customer deactivate';
            }else{
                $customer->state = 1;
                $message='Customer activate';
            }
            $customer->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
