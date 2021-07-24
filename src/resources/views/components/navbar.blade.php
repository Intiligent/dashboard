<el-menu default-active="{{ request()->url() }}" class="el-menu--transparent el-margin-top" :collapse="state.isNavbarCollapse" v-cloak>
    @foreach ($navbarMenu as $group)
        @if ($group->value !== '#')
            <el-menu-item index="{{ $group->url }}">
                <a href="{{ $group->url }}" slot="title">
                    @if ($group->icon)<i class="{{ $group->icon }}"></i>@endif
                    <span>{{ $group->name }}</span>
                </a>
            </el-menu-item>
        @else
            <el-menu-item-group title="{{ $group->name }}">
                @foreach ($group->children as $submenu)
                @if ($submenu->children->count())
                <el-submenu index="{{ $submenu->url }}">
                    <span slot="title">@if ($submenu->icon)<i class="{{ $submenu->icon }}"></i>@endif {{ $submenu->name }}</span>
                    @foreach ($submenu->children as $item)
                    <el-menu-item index="{{ $item->url }}">
                        <a href="{{ $item->url }}"><i class="{{ $item->icon }}"></i>{{ $item->name }}</a>
                    </el-menu-item>
                    @endforeach
                </el-submenu>
                @else
                <el-menu-item index="{{ $submenu->url }}">
                    <a href="{{ $submenu->url }}" slot="title">
                        @if ($submenu->icon)<i class="{{ $submenu->icon }}"></i>@endif
                        <span>{{ $submenu->name }}</span>
                    </a>
                </el-menu-item>
                @endif
                @endforeach
            </el-menu-item-group>
        @endif
    @endforeach

    <hr>

    <el-menu-item index="home">
        <a href="/" target="_blank" slot="title"><i class="el-icon-arrow-left16"></i>Перейти на сайт</a>
    </el-menu-item>
</el-menu>
