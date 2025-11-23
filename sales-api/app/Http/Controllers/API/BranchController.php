<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $page = $request->page;
        $pageSize = $request->pageSize;

        if($search){
            $branches = Branch::where('name','like','%'.$search.'%')
                        ->orderBy('id')
                        ->paginate($pageSize);            
        }else{
            $branches = Branch::orderBy('id')
                                ->paginate($pageSize);
        }

        return response()->json(
            [   'code'  => 200,
                'page'  => $page,
                'total' => $branches->total(),
                'Branches' => ($branches)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                          'name'    => 'required|unique:branches',
                          'address' => 'required',
                          'phone'   => 'required',
                          // 'branch'    => 'required'  //imagen
        ]);
        

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }
        
        $branch = Branch::create($request->all());
        return response()->json(['code' => 200, 
                                 'message'=> 'Branch created']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branch = Branch::where('id','=',$id)->first();

        if(!$branch){
            $data = ['code'=> 404,
                     'message' => 'Branch not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => ($branch)];
        }
        return response()->json(['code' => $data['code'], 
                                 'branch' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {            // method for post, with hasFile
        $validator = Validator::make($request->all(),[
                  'name'    => ['required', Rule::unique('branches')->ignore($id),],
                  'address' => 'required',
                  'phone'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $branch = Branch::find($id);
        if(! $branch){
            $data = ['code'=> 404,
                     'message' => 'branch not found'];
        }else{
            $branch->update($request->all());
            $data = ['code'=> 200, 'message' => 'Branch updated'];
        }

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = Branch::where('id','=',$id)->first();
                
        if(!$branch){
            $data = ['code'=> 404,
                     'message' => 'branch not found'];
        }else{
            if($branch->state == true){
                $branch->state = false;
                $message='Branch deactivate';
            }else{
                $branch->state = true;
                $message='Branch activate';
            }
            $branch->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
