@extends('layout.master')

@section('main-content')
@include('common.crumbs', ['title' => $title,'crumbs'=>['Usuarios',$title]])
    <!-- Container-fluid starts-->
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
                                @empty($data)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Contraseña</label>
                                            <input class="form-control" name="password" type="password" placeholder="password" required>
                                        </div>
                                    </div>
                                @endempty

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Rol</label>
                                        <select class="form-control btn-square" name="rol" required>
                                            <option value="">Selecciona</option>
                                            <option value="Admin"  <?=isset($rol) && $rol=="Admin"? "selected":"";?>>Admin</option>
                                            <option value="Picking"  <?=isset($rol) && $rol=="Empacador"? "selected":"";?>>Picking</option>
                                            <option value="Packing"  <?=isset($rol) && $rol=="Despachador"? "selected":"";?>>Packing</option>
                                            <option value="Delivery"  <?=isset($rol) && $rol=="Delivery"? "selected":"";?>>Delivery </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-control btn-square" name="status" required>
                                            <option value="1"  <?=isset($data['status']) && $data['status']==1? "selected":"";?>>Habilitado</option>
                                            <option value="0"  <?=isset($data['status']) && $data['status']==0? "selected":"";?>>Deshabilitado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit">{{$title}} </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script> 
$(document).ready(function() {
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
