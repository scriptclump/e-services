@section('sideview')
<ul class="page-sidebar-menu page-sidebar-menu-closed"  id="check_side_bar" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
    @if(is_array($roleFeatures) || is_object($roleFeatures))
    @foreach($roleFeatures as $menu)
        @if(isset($menu->name) and ($menu->name != ''))
        <li>
            <a href="<?php echo isset($menu->url) ? URL::asset($menu->url) : ''; ?>">
                <i class="<?php echo isset($menu->icon) ? $menu->icon : ''; ?>"></i> 
                <span class="title">{{ $menu->name }}</span> 
                @if(isset($menu->submenus))
                <span class="arrow "></span>
                @endif
            </a>
            @if(isset($menu->name) and ($menu->name != '') and isset($menu->submenus))
                <ul class="sub-menu">            
                    @foreach($menu->submenus as $submenu)
                    <?php $submenusArr = explode('-', $submenu); ?>
                    <li>
                        <a href="<?php echo (!empty($submenusArr[1])) ? URL::asset($submenusArr[1]) : 'javascript:void(0);'; ?>">
                            <i class="fa fa-angle-right"></i> {{$submenusArr[0]}}
                        </a>
                    </li>
                    @endforeach            
                </ul>
            @endif
        </li>
        @endif
    @endforeach
    @endif
</ul>
@stop