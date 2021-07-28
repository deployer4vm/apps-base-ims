<?php 
$links = \App\Facades\Web::getBreadcrumb();
$linkCount = count($links)-1;
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb m-2 mr-0">
        @foreach($links as $idx => $link)
        @if($idx==$linkCount)
        <li class="breadcrumb-item active" aria-current="page">{!!$link['text']!!}</li>
        @else
        <li class="breadcrumb-item"><a href="{{$link['route']}}">{!!$link['text']!!}</a></li>
        @endif
        @endforeach
    </ol>
</nav>