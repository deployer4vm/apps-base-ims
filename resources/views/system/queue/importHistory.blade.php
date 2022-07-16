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
                <a class="nav-link" href="{{route('system.queue.import.list')}}"><h3 class="m-0">Import Jobs List</h3></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><h3 class="m-0">Import History</h3></a>
            </li>
        </ul>
        
        
    </div>
    <div class="card m-4"> 
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Job Start Time</th>
                        <th>Tenant ID</th>
                        <th>User</th>
                        <th>Import Key</th>
                        <th>Status</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    <?php foreach ($list as $k => $v) { ?>
                    <?php $i++; ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$v['job_start_time']}}</td>
                        <td>{{$v['tenant_id']}}</td>
                        <td>{!!$v['user']?('['.$v['user']['id'].'] <b>'.$v['user']['username'].'</b> <i>('.$v['user']['name'].')</i>'):'-'!!}</td>
                        <td>{{$v['cache_key']}}</td>
                        <td>
                            {{$v['status']}} (
                            @if($v['status']==0)
                                New Process
                            @elseif($v['status']==1)
                                Dispatch
                            @elseif($v['status']==2)
                                Import Sedang Berjalan
                            @elseif($v['status']==3)
                                Import Selesai [Berhasil atau menunggu approve/cancle jika ada approve/cancle]
                            @elseif($v['status']==4)
                                Import Gagal
                            @elseif($v['status']==5)
                                Approve on Process
                            @elseif($v['status']==6)
                                Approve Berhasil [Jobs Selesai]
                            @elseif($v['status']==7)
                                Cancle approve on Process
                            @elseif($v['status']==7)
                                Cancle approve Berhasil [Jobs Selesai]
                            @endif
                            )
                        </td>
                        <td>  
                            <a href="{{route('system.queue.import.detail',['cacheKey'=>$v['cache_key'],'isHistory'=>true])}}" class="btn btn-info btn-xs">
                                <i class="ion ion-md-create"></i> Detail
                            </a> &nbsp; 
                            <a href="{{route('system.queue.import.delete',['cacheKey'=>$v['cache_key'],'isHistory'=>true])}}" class="btn btn-danger btn-xs">
                                <i class="ion ion-md-close"></i> Delete
                            </a> 
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection