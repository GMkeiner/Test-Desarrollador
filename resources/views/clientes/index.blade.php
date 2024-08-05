<!DOCTYPE html>
<html>
<head>
    <title>Laravel DataTables</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="icon" href="{{asset('assets/img/Laravel.png')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container">
    <marquee class="error_Asosiciacion_cliente" behavior="" direction=""> 👁️ Ojo Si El Cliente Tiene Una Venta Asociada No Se Eliminara y Arrojara Un Error 👁️</marquee>
    <h1>Clientes</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewCliente">Crear nuevo Cliente</a>
    <a class="btn btn-info" href="{{route('direcciones.index')}}" id="vista_direcciones">Vista Direcciones</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
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
                <form id="clienteForm" name="clienteForm" class="form-horizontal">
                   <input type="hidden" name="cliente_id" id="cliente_id">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-2 control-label">Nombre</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese Nombre" maxlength="50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        @csrf
                        <label for="email" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-12">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese Email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="col-sm-2 control-label">Teléfono</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ingrese Teléfono" required>
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
            ajax: "{{ route('clientes.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nombre', name: 'nombre'},
                {data: 'email', name: 'email'},
                {data: 'telefono', name: 'telefono'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#createNewCliente').click(function () {
            $('#saveBtn').val("create-cliente");
            $('#cliente_id').val('');
            $('#clienteForm').trigger("reset");
            $('#modelHeading').html("Crear Nuevo Cliente");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editCliente', function () {
            var cliente_id = $(this).data('id');
            $.get("{{ route('clientes.index') }}" + '/' + cliente_id + '/edit', function (data) {
                $('#modelHeading').html("Editar Cliente");
                $('#saveBtn').val("edit-cliente");
                $('#ajaxModel').modal('show');
                $('#cliente_id').val(data.id);
                $('#nombre').val(data.nombre);
                $('#email').val(data.email);
                $('#telefono').val(data.telefono);
            });
        });

        $('#clienteForm').on('submit', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var url = $('#saveBtn').val() === "create-cliente" ? "{{ route('clientes.store') }}" : "{{ route('clientes.update', ':id') }}".replace(':id', $('#cliente_id').val());
            var type = $('#saveBtn').val() === "create-cliente" ? "POST" : "PUT";

            $.ajax({
                data: formData,
                url: url,
                type: type,
                dataType: 'json',
                success: function (data) {
                    $('#clienteForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                    console.log('Response:', data.responseText);
                    $('#saveBtn').html('Guardar cambios');
                }
            });
        });

        $('body').on('click', '.deleteCliente', function () {
            var cliente_id = $(this).data("id");
            
            Swal.fire({
                title: '¿Estas Seguro?',
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
                        url: "{{ route('clientes.store') }}" + '/' + cliente_id,
                        success: function (data) {
                            table.draw();
                            Swal.fire(
                                '¡Eliminado!',
                                'El cliente ha sido eliminado Exitosamente 🖖.',
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

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
