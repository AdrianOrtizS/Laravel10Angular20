<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// use App\Rules\CedulaRule;
use App\Services\DocumentValidator;

class CustomerController extends Controller
{

    public function getSalesCustomer(Request $request)
    {     
        $user = auth()->user();

        // Obtener primer punto de venta del usuario
        $pointOfSale = $user->pointsOfSale()->first();

        if (!$pointOfSale) {
            return response()->json([
                'error' => 'El usuario no tiene puntos de venta asignados'
            ], 403);
        }

        $id_branch = $pointOfSale->id_branch;
        
        if (!$id_branch) {
            return response()->json([
                'error' => 'No se pudo determinar la sucursal del usuario'
            ], 403);
        }
        $customer_id  = $request->customer_id;
        $search       = $request->search;
        $pageSize     = $request->pageSize ?? 10;
        $fecha_ini    = $request->fecha_ini;
        $fecha_fin    = $request->fecha_fin;
        $form_pay     = $request->form_pay;

        // Consulta base
        $query = Sale::where('id_customer', $customer_id)
                      ->where('id_point_of_sale', $pointOfSale->id);

        if ($search) {
            $query->FilterSalesCustomer($search);
        }

        if ($fecha_ini && $fecha_fin) {
            try {
                $inicio = \Carbon\Carbon::parse($fecha_ini)->startOfDay();
                $fin    = \Carbon\Carbon::parse($fecha_fin)->endOfDay();
                $query->whereBetween('created_at', [$inicio, $fin]);
            } catch (\Exception $e) {
                \Log::error('Error al parsear fechas: ' . $e->getMessage());
            }
        }

        if($form_pay)
        {
            $query->where('form_pay', (string)$form_pay);
        }   

        $total_autorizado       = (clone $query)->where('estado_sri', 'AUTORIZADO')
                                                ->sum('total');
        $total_autor_no_autor   = (clone $query)->where('estado_sri', 'NO AUTORIZADO')
                                    ->sum('total');
        // Orden y paginación
        $sales = $query->orderBy('created_at', 'desc')
                      ->paginate($pageSize);

        return response()->json([
            'code'      =>      200,
            'total'     =>      $sales->total(),
            'total_autorizado'      => $total_autorizado,
            'total_autor_no_autor'  => $total_autor_no_autor,
            'Sales'     =>      SaleCollection::make($sales),
        ]);
    }

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
                  'name' => 'required',
                  'tipo_identificador' => 'required|in:cedula,ruc,pasaporte',
                  'num_identificador' => [
                              'required',
                              function ($attribute, $value, $fail) use ($request) {
                                  switch ($request->tipo_identificador) {
                                      case 'cedula':
                                          if (!DocumentValidator::cedula($value))
                                              $fail('Cédula inválida');
                                          break;
                                      case 'ruc':
                                          if (!DocumentValidator::ruc($value))
                                              $fail('RUC inválido');
                                          break;
                                      case 'pasaporte':
                                          if (!DocumentValidator::passport($value))
                                              $fail('Pasaporte inválido');
                                          break;
                                  }
                              }
                          ],
                  'email'   => 'required|email|unique:customers,email',
                  'phone'   => 'required',
                  'address' => 'required',
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
                  'name'  => 'required',

                  'num_identificador' => [
                              'required',
                              function ($attribute, $value, $fail) use ($request) {
                                  switch ($request->tipo_identificador) {
                                      case 'cedula':
                                          if (!DocumentValidator::cedula($value))
                                              $fail('Cédula inválida');
                                          break;
                                      case 'ruc':
                                          if (!DocumentValidator::ruc($value))
                                              $fail('RUC inválido');
                                          break;
                                      case 'pasaporte':
                                          if (!DocumentValidator::passport($value))
                                              $fail('Pasaporte inválido');
                                          break;
                                  }
                              },
                              Rule::unique('customers', 'num_identificador')->ignore($id),
                          ],

                  'email' => [
                            'required',
                            'email', 
                            Rule::unique('customers', 
                            'email')->ignore($id),
                          ],
                  'phone'  => 'required',
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
