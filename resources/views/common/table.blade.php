<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0" style="display:flex;justify-content: space-between;">
                        <h3>{{$title}}</h3>
                        @if ( auth()->user()->getRoleNames()->first() != 'Despachador')
                        <button class="btn btn-primary btn-create" type="button" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal" data-bs-original-title="" title="">Agregar   <i style="color:white;" class="mdi mdi-clipboard-plus"></i></button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="order-history table-responsive">
                            <table id="users-table" class="table table-bordernone display">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
<style>
   table.dataTable thead .sorting_1 {
     background-color: #ccc;
  }
</style>