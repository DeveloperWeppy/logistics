<div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{$title}}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                        @foreach ($crumbs as $crumb)
                          <li class="breadcrumb-item">{{$crumb}}</li>
                        @endforeach
                      
                    </ol>
                </div>
            </div>
        </div>
</div>