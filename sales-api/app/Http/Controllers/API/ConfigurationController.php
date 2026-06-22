<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\Tarifa_iva;
use App\Models\Product;
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

            $configurations->transform(function ($item) {
                if ($item->name === 'logoPdf' && $item->value) {
                    $item->value = asset('storage/' . $item->value);
                }
                return $item;
            });

        }

        
        return response()->json(
            [   'code'  => 200,
                'configurations' => ($configurations)
            ]);
    }

    // public function getTarifas(Request $request)
    // {
    //     $tarifas = Tarifa_iva::orderBy('id')->where('state',1)->get();  
           
    //     return response()->json(
    //         [   'code'  => 200,
    //             'tarifas' => ($tarifas)
    //         ]);
    // }

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
    // public function show(string $id)
    // {
    //     $configuration = Configuration::where('id','=',$id)->first();

    //     if(!$configuration){
    //         $data = ['code'=> 404,
    //                  'message' => 'Configuration not found'];
    //     }else{


    //         $configurations->transform(function ($item) {
    //             if ($item->name === 'logoPdf' && $item->value) {
    //                 $item->value = asset('storage/' . $item->value);
    //             }
    //             return $item;
    //         });



    //         $data = ['code'=> 200,
    //                  'message' => ($configuration )];
    //     }
    //     return response()->json(['code' => $data['code'], 
    //                              'configuration' => $data['message']]);
    // }

public function show(string $id)
{
    $configuration = Configuration::find($id);

    if (!$configuration) {
        return response()->json([
            'code' => 404,
            'message' => 'Configuration not found'
        ]);
    }

    if ($configuration->name === 'logoPdf' && $configuration->value) {
        $configuration->value = env('APP_URL').'storage/'.$configuration->value;
    }                           

    return response()->json([
        'code' => 200,
        'configuration' => $configuration
    ]);
}
    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required', Rule::unique('configurations')->ignore($id)],
            'value' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 403,
                'message' => $validator->errors()
            ]);
        }

        $configuration = Configuration::find($id);

        if (!$configuration) {
            return response()->json([
                'code' => 404,
                'message' => 'Configuration not found'
            ]);
        }

        if ($request->hasFile('file_imagen')) {
            $path = Storage::putFile('logoPdf', $request->file('file_imagen'));

            $configuration->update([
                'name' => $request->name,
                'value' => $path
            ]);
        }


        $configuration->update($request->all());

        return response()->json([
            'code' => 200,
            'message' => 'Configuration updated'
        ]);
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
