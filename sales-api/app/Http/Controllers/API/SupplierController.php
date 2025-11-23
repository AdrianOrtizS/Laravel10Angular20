<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SupplierCollection;
use App\Http\Resources\SupplierResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $suppliers = null;
        $pageSize = $request->pageSize;

        if($search){
            $suppliers = Supplier::where('name', 'like', '%'.$search.'%')
                            ->orderBy('id')
                            ->paginate($pageSize);            
        }else{
            $suppliers = Supplier::orderBy('id')
                            ->paginate($pageSize);
        }



        return response()->json(
            [   'code'  => 200,
                'total' => $suppliers->total(),
                'Suppliers' => SupplierCollection::make($suppliers)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                  'name'        => 'required',
                  'num_identificador' => 'required|unique:Suppliers',
                  'email'       => 'required|email|unique:Suppliers',
                  'phone'       => 'required',
                  'address'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $supplier = Supplier::create($request->all());
        return response()->json(['code' => 200,
                                 'message'=> 'Supplier created',
                                 'supplier'=> $supplier]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::where('state', 1)
                            ->where('id','=',$id)->first();

        if(!$supplier){
            $data = ['code'=> 404,
                     'message' => 'supplier not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => SupplierResource::make($supplier )];
        }
        return response()->json(['code'     => $data['code'], 
                                 'supplier' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */              
    public function update(Request $request, string $id)
    {   
        $validator = Validator::make($request->all(), [
                  'name'        => 'required',
                  'num_identificador' => ['required', Rule::unique('suppliers')->ignore($id),],
                  'email'       => ['required','email', Rule::unique('suppliers')->ignore($id),],
                  'phone'       => 'required',
                  'address'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $supplier = Supplier::find($id);
        if(! $supplier){
            $data = ['code'=> 404,
                     'message' => 'supplier not found'];
        }
        $supplier->update($request->all());
        $data = ['code'=> 200, 'message' => 'supplier updated'];

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::where('id','=',$id)->first();
                
        if(!$supplier){
            $data = ['code'=> 404,
                     'message' => 'supplier not found'];
        }else{
            if($supplier->state == 1){
                $supplier->state = 0;
                $message='supplier deactivate';
            }else{
                $supplier->state = 1;
                $message='supplier activate';
            }
            $supplier->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
