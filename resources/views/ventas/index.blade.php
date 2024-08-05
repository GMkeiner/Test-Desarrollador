<!DOCTYPE html>
<html>
<head>
    <title>Laravel DataTables</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('assets/img/Laravel.png')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>

<div class="container">
    <h1>Ventas</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewVenta"> Crear nueva Venta</a>
    <a class="btn btn-info" href="{{route('clientes.index')}}" id="vista_index">Regresar al index</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Estado</th>
                <th width="150px">Acciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ventaForm" name="ventaForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="venta_id" id="venta_id">
                    <div class="form-group">
                        <label for="cliente_id" class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="cliente_id" name="cliente_id" required="">
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha" class="col-sm-2 control-label">Fecha</label>
                        <div class="col-sm-12">
                            <input type="date" class="form-control" id="fecha" name="fecha" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="monto" class="col-sm-2 control-label">Monto</label>
                        <div class="col-sm-12">
                            <input type="number" step="0.01" class="form-control" id="monto" name="monto" placeholder="Ingrese Monto" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="estado" class="col-sm-2 control-label">Estado</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="estado" name="estado" placeholder="Ingrese Estado" value="" required="">
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ventas.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'cliente_nombre', name: 'cliente_nombre'},
                {data: 'fecha', name: 'fecha'},
                {data: 'monto', name: 'monto'},
                {data: 'estado', name: 'estado'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#createNewVenta').click(function () {
            $('#saveBtn').val("create-venta");
            $('#venta_id').val('');
            $('#ventaForm').trigger("reset");
            $('#modelHeading').html("Crear Nueva Venta");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editVenta', function () {
            var venta_id = $(this).data('id');
            $.get("{{ route('ventas.index') }}" +'/' + venta_id +'/edit', function (data) {
                $('#modelHeading').html("Editar Venta");
                $('#saveBtn').val("edit-venta");
                $('#ajaxModel').modal('show');
                $('#venta_id').val(data.id);
                $('#cliente_id').val(data.cliente_id);
                $('#fecha').val(data.fecha);
                $('#monto').val(data.monto);
                $('#estado').val(data.estado);
            })
        });

        $('#ventaForm').submit(function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var url = $('#saveBtn').val() === "create-venta" ? "{{ route('ventas.store') }}" : "{{ route('ventas.update', ':id') }}".replace(':id', $('#venta_id').val());
            var type = $('#saveBtn').val() === "create-venta" ? "POST" : "PUT";

            $.ajax({
                data: formData,
                url: url,
                type: type,
                dataType: 'json',
                success: function (data) {
                    $('#ventaForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Guardar cambios');
                }
            });
        });

        // Esto hace parte del metodo delete es el mensaje de alerta con sweet alert.
        // para que cada vez que se le de click al boton salga el sweet alert y pregunte antes de eliminar. 

        // $('body').on('click', '.deleteVenta', function () {
        //     var venta_id = $(this).data("id");
            
        //     Swal.fire({
        //         title: '¿Está seguro?',
        //         text: "Despues de esta accion no hay vuelta atras",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Sí, eliminar'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 type: "DELETE",
        //                 url: "{{ route('ventas.destroy', '') }}/" + venta_id,
        //                 success: function (data) {
        //                     table.draw();
        //                     Swal.fire(
        //                         '¡Eliminado!',
        //                         'La dirección ha sido eliminada Exitosamente 🖖.',
        //                         'success'
        //                     );
        //                 },
        //                 error: function (data) {
        //                     console.log('Error:', data);
        //                 }
        //             });
        //         }
        //     });
        // });
    });
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
