<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use DataTables;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Venta::with('cliente')->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('cliente_nombre', function($row){
                    return $row->cliente->nombre;
                })
                // El boton eliminar funciona correctamente solo que lo comente porque en el pdf no dice que una venta debe eliminarse.
                // de igual forma alli queda comentado solo es descomentarlo y listo.
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editVenta">Editar</a>';
                    // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteVenta">Eliminar</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $clientes = Cliente::all();
        return view('ventas.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        Venta::updateOrCreate(
            ['id' => $request->venta_id],
            ['cliente_id' => $request->cliente_id, 'fecha' => $request->fecha, 'monto' => $request->monto, 'estado' => $request->estado]
        );
        return response()->json(['success'=>'Venta guardada correctamente.']);
    }
    
    public function update(Request $request, $id)
{
    $venta = Venta::find($id);

    if (!$venta) {
        return response()->json(['error' => 'Venta no encontrada'], 404);
    }

    try {
        $venta->cliente_id = $request->input('cliente_id');
        $venta->fecha = $request->input('fecha');
        $venta->monto = $request->input('monto');
        $venta->estado = $request->input('estado');
        $venta->save();

        return response()->json(['success' => 'Venta actualizada correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error updating data'], 500);
    }
}

    public function edit($id)
    {
        $venta = Venta::find($id);
        return response()->json($venta);
    }

    // public function destroy($id)
    // {
    //     $venta = Venta::find($id);
    //     $venta->delete();
    //     return response()->json(['success'=>'Venta eliminada correctamente.']);
    // }
}


