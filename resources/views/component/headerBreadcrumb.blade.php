<?php 
/**
 * @param String $title *optional
 * @param String|Array $goback *optional
 */
$title = isset($title)?\App\Facades\Trans::chose($title):\App\Facades\Web::getBreadcrumbTitle();
$goback = isset($goback)?$goback:false;
$goback = is_array($goback)?route($goback[0],isset($goback[1])?$goback[1]:[]):$goback;
?>
<div class="d-flex justify-content-between align-items-center w-100 mb-0 border-bottom">
    <div class="d-flex align-items-center">
        @if($goback)
        <b-btn
            class="p-3 rounded-0 btn btn-outline-default bg-light border-right d-inline-block borderless text-muted text-nowrap" 
            href="{{$goback}}"
        > 
            <span class="ion ion-ios-arrow-back"></span>&nbsp; {{__('lang.back')}}
        </b-btn>
        @endif
        <h5 class="p-3 pl-4 m-0 d-inline-block text-nowrap font-weight-normal">
            {{$title}}
        </h5>
    </div>
    <div class="p-2 pr-4 text-right">
        @include("component.breadcrumb")
    </div>
</div>