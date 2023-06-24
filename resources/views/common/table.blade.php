<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-pc">
                    <div class="card-header pb-0" style="display:flex;justify-content: space-between;">
                        @if (Request::fullUrl() == Request::root().'/users')
                            <h3>Lista de Usuarios</h3>
                        @else
                            <h3>{{Request::fullUrl() == Request::root().'/orders' ? 'Activos' : 'Completados'}}</h3>
                        @endif
                        
                        @if ( auth()->user()->getRoleNames()->first() != 'Despachador')
                        <button class="btn btn-primary btn-create" type="button" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal" data-bs-original-title="" title="">Agregar   <i style="color:white;" class="mdi mdi-clipboard-plus"></i></button>
                        @endif
                    </div>
                    
                    <div class="card-body">
                        {{-- <div class="order-history table-responsive"> --}}
                            <table id="users-table" class="display responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                    @foreach ($titles as $name)
                                    <th scope="col">{{$name}}</th>
                                    @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                           
                        {{-- </div> --}}
                    </div>
                </div>

                <div id="tarjeta-table">
                    @if ( auth()->user()->getRoleNames()->first() != 'Despachador')
                        <button class="btn btn-primary btn-create" style="margin-bottom:10px" type="button" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal" data-bs-original-title="" title="">Agrega pedido   <i style="color:white;" class="mdi mdi-clipboard-plus"></i></button>
                    @endif
                </div>
            </div>
        </div>
    </div>
   
<style>
   table.dataTable thead .sorting_1 {
     background-color: #ccc;
  }
</style>