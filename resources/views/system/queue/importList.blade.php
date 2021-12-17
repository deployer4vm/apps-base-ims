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
                <a class="nav-link active" href="#"><h3 class="m-0">Import Jobs List</h3></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('system.queue.import.history')}}"><h3 class="m-0">Import History</h3></a>
            </li>
        </ul>
        
        
    </div>
    <div class="card m-4"> 

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>JOB ID</th>
                        <th>Queue</th>
                        <th>User</th>
                        <th>Start Time</th>
                        <th>Import Key</th>
                        <th>Data Count</th>
                        <th>Attempts</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach($jobs as $v)
                    <?php $i++; ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$v['id']}}</td> 
                        <td>{{$v['queue']}}</td> 
                        <td>{!!$v['user']?('['.$v['user']['id'].'] <b>'.$v['user']['username'].'</b> <i>('.$v['user']['name'].')</i>'):'-'!!}</td> 
                        <td>{{$v['import']['inputTime']}}</td>
                        <!-- <td><?php echo $v['reserved_at']?date('Y-m-d H:i:s',$v['reserved_at']):''; ?></td>  -->
                        <td>{{$v['formated_payload']['data']['command']['cacheKey']}}</td>
                        <td>{{isset($v['import']['count'])?(number_format($v['import']['count'],0,',','.').'<br>'):''}}<i>(processed <b>{{number_format($v['import']['processedCount'],0,',','.')}}</b>)</i></td>
                        <td>                            
                            @if($v['attempts']>=1)
                            <div class="badge badge-success">
                                Jobs sedang berjalan
                            </div>   
                            
                            <a href="{{route('system.queue.import.cancel',['cacheKey'=>$v['formated_payload']['data']['command']['cacheKey']])}}" class="btn btn-danger btn-xs">
                                <i class="ion ion-md-close"></i> Cancel Job
                            </a>                          
                            @endif
                        </td> 
                        <td>  
                            <a href="{{route('system.queue.import.detail',['cacheKey'=>$v['formated_payload']['data']['command']['cacheKey']])}}" class="btn btn-info btn-xs">
                                <i class="ion ion-md-create"></i> Detail
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