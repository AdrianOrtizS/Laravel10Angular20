<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use Illuminate\Support\Facades\Storage;
// use App\Http\Resources\CategorieCollection;
// use App\Http\Resources\CategorieResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CategorieController extends Controller
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
            $categories = Categorie::where('name','like','%'.$search.'%')
                        ->orderBy('id')
                        ->paginate($pageSize);
        }else{
            $categories = Categorie::orderBy('id')
                            ->paginate($pageSize);
        }


        return response()->json(
            [   'code'  => 200,
                'page'  => $page,
                'total' => $categories->total(),
                'Categories' => ($categories)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                          'name'        => 'required|unique:categories',
                          'description' => 'required',
                          // 'categorie'    => 'required'  //imagen
        ]);
        

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        if($request->hasFile('categorie')){
            $path = Storage::putFile('categories', $request->file('categorie'));
            $request->request->add(['imagen' => $path]);
        }
        
        $categorie = Categorie::create($request->all());
        return response()->json(['code' => 200, 
                                 'message'=> 'Categorie created']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categorie = Categorie::where('id','=',$id)->first();

        if(!$categorie){
            $data = ['code'=> 404,
                     'message' => 'Categorie not found'];
        }else{
            $data = ['code'=> 200,
                     'message' => ($categorie )];
        }
        return response()->json(['code' => $data['code'], 
                                 'categorie' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {            // method for post, with hasFile
        $validator = Validator::make($request->all(),[
                  'name' => ['required', Rule::unique('categories')->ignore($id),],
                  'description' => 'required',
                  //'categorie'   => 'required'  //imagen
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $categorie = Categorie::find($id);
        if(! $categorie){
            $data = ['code'=> 404,
                     'message' => 'Categorie not found'];
        }else{
            if($request->hasFile('categorie')){
                if($categorie->imagen){
                    Storage::delete($categorie->imagen);
                }
                $path = Storage::putFile('categories', $request->file('categorie'));
                $request->request->add(['imagen' => $path]);
            }
            $categorie->update($request->all());
            $data = ['code'=> 200, 'message' => 'Categorie updated'];
        }

        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categorie = Categorie::where('id','=',$id)->first();
                
        if(!$categorie){
            $data = ['code'=> 404,
                     'message' => 'Categorie not found'];
        }else{
            if($categorie->state == true){
                $categorie->state = false;
                $message='Categorie deactivate';
            }else{
                $categorie->state = true;
                $message='Categorie activate';
            }
            $categorie->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }
}
