<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Support\Facades\Storage;
// use App\Http\Resources\ConfigurationCollection;
// use App\Http\Resources\ConfigurationResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        
        if($search){
            $configurations = Configuration::where('name','like','%'.$search.'%')
                                            ->orderBy('id')
                                            ->get();          
        }else{
            $configurations = Configuration::orderBy('id')
                                            ->get();  
        }

        
        return response()->json(
            [   'code'  => 200,
                'configurations' => ($configurations)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                          'name'    => 'required|unique:configurations',
                          'value'   => 'required'        
                      ]);
        
        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }
    
        $configuration = Configuration::create($request->all());
        return response()->json(['code' => 200, 
                                 'message'=> 'Configuration created']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $configuration = Configuration::where('id','=',$id)->first();

        if(!$configuration){
            $data = ['code'=> 404,
                     'message' => 'Configuration not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => ($configuration )];
        }
        return response()->json(['code' => $data['code'], 
                                 'configuration' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {            // method for post, with hasFile
        $validator = Validator::make($request->all(),[
                  'name' => ['required', Rule::unique('configurations')->ignore($id),],
                  'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $configuration = Configuration::find($id);
        if(! $configuration){
            $data = ['code'=> 404,
                     'message' => 'Configuration not found'];
        }else{
            $configuration->update($request->all());
            $data = ['code'=> 200, 'message' => 'Configuration updated'];
        }

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $configuration = Configuration::where('id','=',$id)->first();
                
        if(!$configuration){
            $data = ['code'=> 404,
                     'message' => 'Configuration not found'];
        }else{
            if($configuration->state == true){
                $configuration->state = false;
                $message='Configuration deactivate';
            }else{
                $configuration->state = true;
                $message='Configuration activate';
            }
            $configuration->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }
}
