<!DOCTYPE html>
<html>
<head>
    <title>Laravel DataTables</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="icon" href="{{asset('assets/img/Laravel.png')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>

<div class="container">
    <marquee class="error_Asosiciacion_cliente" behavior="" direction=""> 👁️ Ojo no se podra eliminar una direccion si el cliente tiene una venta asociada 👁️</marquee>
    <h1>Direcciones</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewDireccion">Crear nueva Dirección</a>
    <a class="btn btn-info" href="{{route('ventas.index')}}" id="vista_ventas">Vista Ventas</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Cliente</th>
                <th>Dirección</th>
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
                <form id="direccionForm" name="direccionForm" class="form-horizontal">
                   <input type="hidden" name="direccion_id" id="direccion_id">
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
                        <label for="direccion" class="col-sm-2 control-label">Dirección</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese Dirección" value="" required="">
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
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('direcciones.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'cliente_nombre', name: 'cliente_nombre'},
                {data: 'direccion', name: 'direccion'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#createNewDireccion').click(function () {
            $('#saveBtn').val("create-direccion");
            $('#direccion_id').val('');
            $('#direccionForm').trigger("reset");
            $('#modelHeading').html("Crear Nueva Dirección");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editDireccion', function () {
            var direccion_id = $(this).data('id');
            $.get("{{ route('direcciones.index') }}" + '/' + direccion_id + '/edit', function (data) {
                $('#modelHeading').html("Editar Dirección");
                $('#saveBtn').val("edit-direccion");
                $('#ajaxModel').modal('show');
                $('#direccion_id').val(data.id);
                $('#cliente_id').val(data.cliente_id);
                $('#direccion').val(data.direccion);
            });
        });

        $('#direccionForm').on('submit', function (e) {
            e.preventDefault();
            var url = $('#saveBtn').val() === "create-direccion" ? "{{ route('direcciones.store') }}" : "{{ route('direcciones.update', ':id') }}".replace(':id', $('#direccion_id').val());
            var type = $('#saveBtn').val() === "create-direccion" ? "POST" : "PUT";

            $.ajax({
                data: $(this).serialize(),
                url: url,
                type: type,
                dataType: 'json',
                success: function (data) {
                    $('#direccionForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Guardar cambios');
                }
            });
        });

        $('body').on('click', '.deleteDireccion', function () {
            var direccion_id = $(this).data("id");

            Swal.fire({
                title: '¿Está seguro?',
                text: "Despues de esta accion no hay vuelta atras",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('direcciones.store') }}" + '/' + direccion_id,
                        success: function (data) {
                            table.draw();
                            Swal.fire(
                                '¡Eliminado!',
                                'La dirección ha sido eliminada Exitosamente 🖖.',
                                'success'
                            );
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            });
        });
    });
</script>

</body>
</html>
