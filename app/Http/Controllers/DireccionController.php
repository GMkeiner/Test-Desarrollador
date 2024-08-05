<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Direccion;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class DireccionController extends Controller
{
    public function index(Request $request)
    {
        Log::info('DireccionController@index - Request received');

        if ($request->ajax()) {
            try {
                $data = Direccion::with('cliente')->latest()->get();
                Log::info('DireccionController@index - Data fetched successfully');

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('cliente_nombre', function($row) {
                        return $row->cliente->nombre;
                    })
                    ->addColumn('action', function($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editDireccion">Editar</a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteDireccion">Eliminar</a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DireccionController@index - Error: '.$e->getMessage());
                return response()->json(['error' => 'Error fetching data'], 500);
            }
        }

        $clientes = Cliente::all();
        return view('direcciones.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        Log::info('DireccionController@store - Request data: ', $request->all());

        try {
            Direccion::updateOrCreate(
                ['id' => $request->direccion_id],
                ['cliente_id' => $request->cliente_id, 'direccion' => $request->direccion]
            );

            Log::info('DireccionController@store - Dirección guardada correctamente');
            return response()->json(['success' => 'Dirección guardada correctamente.']);
        } catch (\Exception $e) {
            Log::error('DireccionController@store - Error: '.$e->getMessage());
            return response()->json(['error' => 'Error saving data'], 500);
        }
    }

     public function update(Request $request, $id)
{
    Log::info('DireccionController@update - ID: '.$id);

    try {
        $direccion = Direccion::find($id);
        
        if (!$direccion) {
            return response()->json(['error' => 'Dirección no encontrada'], 404);
        }

        $direccion->cliente_id = $request->input('cliente_id');
        $direccion->direccion = $request->input('direccion');
        $direccion->save();

        Log::info('DireccionController@update - Dirección actualizada correctamente');
        return response()->json(['success' => 'Dirección actualizada correctamente.']);
    } catch (\Exception $e) {
        Log::error('DireccionController@update - Error: '.$e->getMessage());
        return response()->json(['error' => 'Error updating data'], 500);
    }
}


    public function edit($id)
    {
        Log::info('DireccionController@edit - ID: '.$id);

        try {
            $direccion = Direccion::find($id);
            return response()->json($direccion);
        } catch (\Exception $e) {
            Log::error('DireccionController@edit - Error: '.$e->getMessage());
            return response()->json(['error' => 'Error fetching data'], 500);
        }
    }

    public function destroy($id)
    {
        Log::info('DireccionController@destroy - ID: '.$id);

        try {
            $direccion = Direccion::find($id);
            if (!$direccion) {
                return response()->json(['error' => 'Dirección no encontrada'], 404);
            }

            if ($direccion->cliente->ventas()->exists()) {
                return response()->json(['error' => 'No se puede eliminar la dirección con ventas asociadas.']);
            }

            $direccion->delete();
            return response()->json(['success' => 'Dirección eliminada correctamente.']);
        } catch (\Exception $e) {
            Log::error('DireccionController@destroy - Error: '.$e->getMessage());
            return response()->json(['error' => 'Error deleting data'], 500);
        }
    }
}
