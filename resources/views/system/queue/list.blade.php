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
    @include('component.alert')
    <div class="card m-4"> 

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
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
                        <td>{{$v}}</td>
                        <td>  
                            <a href="{{route('system.queue.detail',['cacheKey'=>$v])}}">
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