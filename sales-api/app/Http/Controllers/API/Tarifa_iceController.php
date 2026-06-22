<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarifa_ice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class Tarifa_iceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $tarifas_ice = null;
        $pageSize = $request->pageSize;

        if($search){
            $tarifas_ice = Tarifa_ice::where('codigo_porcentaje', 'like', '%'.$search.'%')
                            ->orderBy('id')
                            ->paginate($pageSize);            
        }else{
            $tarifas_ice = Tarifa_ice::orderBy('id')
                            ->paginate($pageSize);
        }

        return response()->json(
            [   'code'  => 200,
                'total' => $tarifas_ice->total(),
                'iceTarifas' => ($tarifas_ice)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'codigo'  => 'required',            //     3 -> ice  -  5 -> IRBPNR
          'codigo_porcentaje'=> 'required',   //  3041 -> Perfumes 20%
          'descripcion'=> 'required',         //  Perfumes y aguas de tocador
          'tipo' => 'required|in:PORCENTAJE,ESPECIFICO,MIXTO', //  porcentaje -  especifico  -  mixto
          'tarifa'  => 'required',            //  5,00%      -  0,18$       -  0,16 
          
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $tarifa_ice = Tarifa_ice::create($request->all());
        return response()->json(['code' => 200,
                                 'message'=> 'ice tarifa created',
                                 'iceTarifa'=> $tarifa_ice]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tarifa_ice = Tarifa_ice::where('id','=',$id)->first();

        if(!$tarifa_ice){
            $data = ['code'=> 404,
                     'message' => 'Ice Tarifa not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => ($tarifa_ice )];
        }
        return response()->json(['code' => $data['code'], 
                                 'iceTarifa' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */              
    public function update(Request $request, string $id)
    {   
        $validator = Validator::make($request->all(), [
          'codigo'  => 'required',            //     3 -> ice  -  5 -> IRBPNR
          'codigo_porcentaje'=> 'required',   //  3041 -> Perfumes 20%
          'descripcion'=> 'required',         //  Perfumes y aguas de tocador
          'tipo'    => 'required',            //  porcentaje -  especifico  -  mixto
          'tarifa'  => 'required',            //  5,00%      -  0,18$       -  0,16 
          // 'unidad'  => 'required',            //             -  unidad      -  litro
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $tarifa_ice = Tarifa_ice::find($id);
        if(! $tarifa_ice){
            $data = ['code'=> 404,
                     'message' => 'Ice Tarifa not found'];
        }
        $tarifa_ice->update($request->all());
        $data = ['code'=> 200, 'message' => 'Ice Tarifa updated'];

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tarifa_ice = Tarifa_ice::where('id','=',$id)->first();
                
        if(!$tarifa_ice){
            $data = ['code'=> 404,
                     'message' => 'Ice Tarifa not found'];
        }else{
            if($tarifa_ice->estado==1){
                $tarifa_ice->estado = 0;
                $message='Ice Tarifa deactivate';
            }else{
                $tarifa_ice->estado = 1;
                $message='Ice Tarifa activate';
            }
            $tarifa_ice->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
