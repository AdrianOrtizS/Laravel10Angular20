<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    	$search = $request->search;
        $user = null;
        $pageSize = $request->pageSize;

        if($search){
            $users = User::FilterUser($search   )
                            ->orderBy('id')
                            ->paginate($pageSize);            
        }else{
            $users = User::orderBy('id')
                            ->paginate($pageSize);
        }

        return response()->json(
            [   'code'  => 200,
                'total' => $users->total(),
				'users'  => $users->map(function ($user) {
							    return [
							        'id'    => $user->id,
							        'name'  => $user->name,
							        'email' => $user->email,
							        'role'  => $user->role,
							   		'state'  => $user->state,
							   		'sucursal_name_estab' => optional($user->pointsOfSale->branch)->name,
						            'sucursal_num_estab'  => optional($user->pointsOfSale->branch)->num_establecimiento,
						            'point_of_sale'       => optional($user->pointsOfSale)->codigo_punto_emision,
								];
						})
            ]);
    }

    public function getBranches()
    {
		$branches = Branch::orderBy('id')->get();

        return response()->json(
            [   'code'  => 200,
                'branches'  => $branches->map(function ($branch) {
					    return [
					        'id'    => $branch->id,
					        'name'  => $branch->name,
					        'address' => $branch->address,
					        'num_establecimiento'  => $branch->num_establecimiento,
					   		'phone'  => $branch->phone,
					   		'state'  => $branch->state,
						];
				})
            ]);
    }


    public function getPointsOfSale(Request $request)
    {
		$point_of_sales = PointsOfSale::where('id_branch', $request->id_branch)
										->get();

        return response()->json(
            [   'code'  => 200,
                'branches'  => $branches->map(function ($branch) {
					    return [
					        'id'    => $branch->id,
					        'name'  => $branch->name,
					        'address' => $branch->address,
					        'num_establecimiento'  => $branch->num_establecimiento,
					   		'phone'  => $branch->phone,
					   		'state'  => $branch->state,
						];
				})
            ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('id','=',$id)->first();

        if(!$user){

            $data = ['code'		=> 404,
                     'message' 	=> 'User not found'];
        }else{
            $data = ['code'		=> 200,
                     'message' 	=> [
							        'id'    => $user->id,
							        'name'  => $user->name,
							        'email' => $user->email,
							        'role'  => $user->role,
                                    'imagen'=> $user->imagen ? env('APP_URL').'storage/'.$user->imagen: null,
							   		'sucursal_name_estab' => optional($user->pointsOfSale->branch)->name,
						            'sucursal_num_estab'  => optional($user->pointsOfSale->branch)->num_establecimiento,
						            'point_of_sale'       => optional($user->pointsOfSale)->codigo_punto_emision,
								   ]

                 ];
        }

        return response()->json(['code' => $data['code'], 
                                 'User' => $data['message']]);
    }

    /**
     * Update the specified resource in storage.
     */              
    public function update(Request $request, string $id)
    {   
        $validator = Validator::make($request->all(), [
                  	        'name'	=>	'required',
				        	// 'email' => ['required','email', Rule::unique('users')->ignore($id),],
                  			'role' 	=>	'required',
				        	'id_point_of_sale' 	=>	'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        $user = User::find($id);
        if(! $user){
            $data = ['code'=> 404,
                     'message' => 'User not found'];
        }
        $user->update($request->all());
        $data = [
        			'code'		=> 200, 
        			'message' 	=> 'user updated',
        			'user' 		=> $user];

        return response()->json([	$data['code'], 
                                 	$data['message'],
                             		$data['user']]
                             	);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id','=',$id)->first();
                
        if(!$user){
            $data = ['code'=> 404,
                     'message' => 'User not found'];
        }else{
            if($user->state == 1){
                $user->state = 0;
                $message= 'User deactivate';
            }else{
                $user->state = 1;
                $message ='User activate';
            }
            $user->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

}
