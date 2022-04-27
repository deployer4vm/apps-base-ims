@extends('layouts.frontend')

@section('styles')
    
@endsection

@section('scripts')
@parent
    <script>
    </script>
@endsection


@section('content')
<div id="vueListMesinAbsen">

    <div class="text-center m-4">
        @include('component.alert')   
    </div>

    <div class="text-center my-2 mx-4">
        
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{route('system.queue.export.list')}}"><h3 class="m-0">Export Jobs List</h3></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><h3 class="m-0">Export History</h3></a>
            </li>
        </ul>
        
        
    </div>
    <div class="card m-4"> 
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tenant ID</th>
                        <th>User</th>
                        <th>Export Key</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach($list as $v)
                    <?php $i++; ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$v['tenant_id']}}</td>
                        <td>{!!$v['user']?('['.$v['user']['id'].'] <b>'.$v['user']['username'].'</b> <i>('.$v['user']['name'].')</i>'):'-'!!}</td>
                        <td>{{$v['cache_key']}}</td>
                        <td>  
                            <a href="{{route('system.queue.export.detail',['cacheKey'=>$v['cache_key'],'isHistory'=>true])}}" class="btn btn-info btn-xs">
                                <i class="ion ion-md-create"></i> Detail
                            </a> &nbsp; 
                            <a href="{{route('system.queue.export.delete',['cacheKey'=>$v['cache_key'],'isHistory'=>true])}}" class="btn btn-danger btn-xs">
                                <i class="ion ion-md-close"></i> Delete
                            </a> 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection