<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cliente::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editCliente">Editar</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteCliente">Eliminar</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('clientes.index');
    }

    public function store(Request $request)
    {
        Cliente::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ]);
        return response()->json(['success' => 'Cliente guardado correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if ($cliente) {
            $cliente->nombre = $request->nombre;
            $cliente->email = $request->email;
            $cliente->telefono = $request->telefono;
            $cliente->save();

            return response()->json(['success' => 'Cliente actualizado correctamente.']);
        }

        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    public function destroy(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        // Mostrar mensaje en consola de error el cual sera visualizado si el cliente tiene una venta asociado y lo devulev en JSON.
        // si pasa lo contrario el cliente se elimina normal 
        if ($cliente) {
            if ($cliente->ventas()->exists() || $cliente->direcciones()->exists()) {
                return response()->json(['error' => 'No se puede eliminar el cliente con ventas o direcciones asociadas.'], 403);
            }

            $cliente->delete();
            return response()->json(['success' => 'Cliente eliminado correctamente.']);
        }

        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    public function edit($id)
    {
        $cliente = Cliente::find($id);

        if ($cliente) {
            return response()->json($cliente);
        }

        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }
}
