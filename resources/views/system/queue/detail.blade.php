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
    <div class="d-flex justify-content-between align-items-center w-100 mb-0 border-bottom">
        <div class="d-flex align-items-center">
            <a
                class="p-3 rounded-0 btn btn-outline-default bg-light border-right d-inline-block borderless text-muted text-nowrap" 
                href="{{route($isHistory?'system.queue.export.history':'system.queue.export.list')}}"
            > 
                <span class="ion ion-ios-arrow-back"></span>&nbsp; {{__('lang.back')}}
            </a>
            
            <h5 class="p-3 pl-4 m-0 d-inline-block text-nowrap font-weight-normal">
                {{$data['cacheKey']}}
            </h5>
        </div>
    </div>
    @include('component.alert')
    <div class="card m-4"> 
        <div class="row m-2">
            <div class="col">
                User : 
                @if($data['user'])<b>{{$data['user']['name']}} <i>({{$data['user']['username']}})</i></b>@endif
            </div>
            <div class="col">
                Status Jobs : 
                @if(isset($data['jobs'][0]['attempts']))                
                    @if($data['jobs'][0]['attempts']==1)
                    <div class="badge badge-success">
                        Jobs sedang berjalan
                    </div> 
                    @else
                    <div class="badge badge-info">
                        Jobs belum berjalan
                    </div> 
                    @endif
                @else
                <div class="badge badge-default">
                    Tidak Ada Jobs
                </div> 
                @endif
            </div>            
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Key</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach($data as $k => $v)
                    <?php 
                    if(!in_array($k,['jobs','user'])){
                        $i++;
                    ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$k}}</td>
                        <td>
                        <?php 
                        
                        if(is_array($v)){
                            $var = '<pre>'.var_export($v,true) . '</pre>';
                        }else{
                            $var = $v;
                        } 
                        
                        if(in_array($k,['log','listingParams','template'])){
                            ?>
                            <div style="overflow-y: scroll; max-height: 300px; width: 100%;">
                            {!!$var!!}
                            </div>
                            <?php
                        }else{
                            echo $var;
                            if($k=='status'){
                                echo ' (';
                                if($var==0){
                                    echo 'New Process';
                                }else if($var==1){
                                    echo 'Dispatch';
                                }else if($var==2){
                                    echo 'Jobs Sedang Berjalan';
                                }else if($var==3){
                                    echo 'Jobs Selesai';
                                }else if($var==4){
                                    echo 'Jobs Gagal';
                                }
                                echo ')';
                            }
                        }
                        ?>
                        </td>
                    </tr>
                    <?php } ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection