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
                href="{{route('system.queue.list')}}"
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

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
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
                    <?php $i++; ?>
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$k}}</td>
                        <td><?php if(is_array($v)){
                            echo '<pre>'.var_export($v,true) . '</pre>';
                        }else{
                            echo $v;
                         } ?></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection