<!-- Layout footer -->
<nav class="layout-footer footer bg-footer-theme">
    <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
        <div class="pt-3">
            <span class="footer-text font-weight-bolder">
                {{ config('AppConfig.system.template.admin.footer.text')  }}
            </span>
        </div>
        <div>
            <?php 
            foreach (config('AppConfig.system.template.admin.footer.menu',[]) as $menu) { 
                if(isset($menu['route']['name'])){
            ?>
                <a href="{{$menu['route']}}" class="footer-link pt-3 ml-4 {{$menu['class']}}">{{ $menu['caption'] }}</a>
            <?php
                }else{
            ?>
                <a href="{{route($menu['route']['name'])}}" class="footer-link pt-3 ml-4 {{$menu['class']}}">{{ $menu['caption'] }}</a>
            <?php
                }
            } 
            ?>
            <!-- <a href="javascript:void(0)" class="footer-link pt-3">About Us</a>
            <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Help</a>
            <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Contact</a>
            <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Terms &amp; Conditions</a> -->
        </div>
    </div>
</nav>
<!-- / Layout footer -->
