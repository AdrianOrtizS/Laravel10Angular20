<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PointsOfSale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Branch;

class PointsOfSaleController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        $search = $request->search;
        $page   = $request->page;
        $pageSize = $request->pageSize;

        if($search){
            $pointsOfSale = PointsOfSale::with('Branch:id,name')
                        ->where('codigo_punto_emision','like','%'.$search.'%')
                        ->orderBy('id')
                        ->paginate($pageSize);            
        }else{
            $pointsOfSale = PointsOfSale::with('Branch:id,name')
                                ->orderBy('id')
                                ->paginate($pageSize);
        }

        return response()->json(
            [   'code'  => 200,
                'page'  => $page,
                'total' => $pointsOfSale->total(),
                'PointsOfSale' => ($pointsOfSale)
            ]);
    }

    public function getPointsByBranch(string $id)
    {
        $pointsOfSale = PointsOfSale::where('id_branch', $id)
                                    ->get();// with('Branch:id,name')
                    
        return response()->json(
            [   'code'  => 200,
                'pointsOfSale' => ($pointsOfSale)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                      'id_branch'            => 'required',  
                      'codigo_punto_emision' => 'required',
                      'secuencial_actual'    => 'required',
                      'descripcion'          => 'required', //Ej. maquina 1
        ]);
        
        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }
        
        $pointsOfSale = PointsOfSale::create($request->all());
        return response()->json(['code' => 200, 
                                 'message'=> 'Points Of Sale created']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pointsOfSale = PointsOfSale::where('id','=',$id)->first();

        if(!$pointsOfSale){
            $data = ['code'=> 404,
                     'message' => 'Points Of Sale not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => ($pointsOfSale)];
        }
        return response()->json(['code' => $data['code'], 
                                 'pointsOfSale' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {            
        $validator = Validator::make($request->all(),[
                          'id_branch'            => 'required',
                          'codigo_punto_emision' => 'required',
                          'secuencial_actual'    => 'required',
                          'descripcion'          => 'required', //Ej. maquina 1
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $pointsOfSale = PointsOfSale::find($id);
        if(! $pointsOfSale){
            $data = ['code'=> 404,
                     'message' => 'Points Of Sale not found'];
        }else{
            $pointsOfSale->update($request->all());
            $data = ['code'=> 200, 'message' => 'Points Of Sale updated'];
        }

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pointsOfSale = PointsOfSale::where('id','=',$id)->first();
                
        if(!$pointsOfSale){
            $data = ['code'=> 404,
                     'message' => 'Points Of Sale not found'];
        }else{
            if($pointsOfSale->state == true){
                $pointsOfSale->state = false;
                $message = 'pointsOfSaledeactivate';
            }else{
                $pointsOfSale->state = true;
                $message = 'Points Of Sale activate';
            }
            $pointsOfSale->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
