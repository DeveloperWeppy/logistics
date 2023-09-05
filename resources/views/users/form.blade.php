@extends('layout.master')

@section('title')
    <?=isset($data)? 'Editar' : 'Crear'?> Usuario
@endsection
@section('main-content')
@include('common.crumbs', ['title' => $title,'crumbs'=>['Usuarios',$title]])
    <div class="container-fluid">
        <div class="edit-profile">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <form class="card" id="from_user"  action="<?=isset($data)?route('users.update',['id'=>$data['id']]):route('users.store')?>" method="post">
                        @csrf {{ csrf_field() }}
                        <div class="card-header pb-0">
                            <h2 class="card-title mb-0">{{$title}}</h2>
                            <div class="card-options"><a class="card-options-collapse" href="javascript:void(0)"
                                    data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a
                                    class="card-options-remove" href="javascript:void(0)" data-bs-toggle="card-remove"><i
                                        class="fe fe-x"></i></a></div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label">Nombres</label>
                                        <input class="form-control" name="name" type="text" placeholder="Nombres" value="<?=isset($data['name']) ? $data['name']:"";?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-5">
                                    <div class="mb-3">
                                        <label class="form-label">Apellidos</label>
                                        <input class="form-control" name="last_name" type="text" placeholder="Apellidos" value="<?=isset($data['last_name']) ? $data['last_name']:"";?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" name="email" type="email" placeholder="Email" value="<?=isset($data['email']) ? $data['email']:"";?>" required>
                                    </div>
                                </div>
                               
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña</label>
                                            @if ($title=="Mi Perfil")
                                               <input class="form-control" name="password" type="password" placeholder="password" >
                                            @elseif (isset($data))
                                               <input class="form-control" name="password" type="password" placeholder="password" >
                                            @else
                                               <input class="form-control" name="password" type="password" placeholder="password" required>
                                            @endif
                                        </div>
                                    </div>
                                

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Rol </label>
                                        @if ($title=="Mi Perfil")
                                            <input class="form-control" type="text" value="<?= implode(',', $rol);?>" readonly>
                                        @else
                                            <select class="form-control btn-square" id="select-rol" name="rol[]" required multiple>
                                                <option value="Admin">Admin</option>
                                                <option value="Picking" >Picking</option>
                                                <option value="Packing" >Packing</option>
                                                <option value="Delivery">Delivery</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        @if ($title=="Mi Perfil")
                                            <input class="form-control" type="text" value="<?= $data['status']==1? 'Habilitado':"Deshabilitado" ?>" readonly>
                                        @else
                                            <select class="form-control btn-square" name="status" required>
                                                <option value="1"  <?=isset($data['status']) && $data['status']==1? "selected":"";?>>Habilitado</option>
                                                <option value="0"  <?=isset($data['status']) && $data['status']==0? "selected":"";?>>Deshabilitado</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            @if ($title=="Mi Perfil")
                                <button class="btn btn-primary" type="submit">Modificar </button>
                            @else
                                <button class="btn btn-primary" type="submit">{{$title}} </button>
                            @endif
                         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php
if (!isset($rol)) {
    $rol = [];
}
?>
<script> 
$(document).ready(function() {
    var roles = {!! json_encode($rol) !!};
    $('#select-rol').select2({
        multiple: true
    });
    $('#select-rol').val(roles).trigger('change');
    $('#from_user').on('submit', function(e) {
        swal({
            title: 'Guardando ',
            text: 'Por Favor espere',
            timer: 2000,
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false,
            allowOutsideClick: false, 
        });
        e.preventDefault();
        const formData = new FormData(this);

        fetch($(this).attr('action'), {
            method: $(this).attr('method'),
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status){
                mensaje("success",<?=isset($data['id']) ? '"Modificado","El Usuario fue modificado"':'"Creado","El Usuario fue creado"';?>);
                setTimeout(() => {location.href='{{route('users')}}';}, 1000);
            }else{
                mensaje("error","Error",data.msj);
            }
        })
        .catch(error => {
            console.error(error);
        });
    });
    $('#cancelButton').on('click', function(e) {
        e.preventDefault(); 
    });
});
function mensaje(icon,title,text){
        swal({
            icon: icon,
            title:title,
            text: text,
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
            });

    }
</script>
@endsection
