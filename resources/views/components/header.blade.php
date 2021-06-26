<header class="el-container header-container">
    <nav class="el-grid el-grid-sm el-flex el-flex-middle">
        <div class="el-width-auto header-logo-container">
            <a href="{{ route('dashboard.home') }}">
                <span class="el-grid el-grid-sm el-flex el-flex-middle el-height-inherit">
                    <span class="el-width-auto">
                        <span class="el-avatar el-avatar--large el-avatar--circle">
                            <img src="/img/dashboard/logo-square.png?v=2" style="object-fit: cover;">
                        </span>
                    </span>
                    <span class="el-width-expand">
                        <div class="logo-title el-text-truncate">{{ config('APP_NAME', 'Laravel') }}</div>
                        <div class="logo-description el-text-truncate">{{ config('META_TITLE', 'Project description') }}</div>
                    </span>
                </span>
            </a>
        </div>
        <div class="el-width-auto">
            <el-button class="is-narrow" icon="el-icon-menu7" plain type="primary" size="medium" @click="onNavbarToggle"></el-button>
        </div>
        @section('header.center')
        <div class="el-width-expand">
            <form class="" method="get">
                <el-input name="q" placeholder="Type some text to search" prefix-icon="el-icon-search4" size="medium" v-model="q" clearable>
                    <el-button native-type="submit" slot="append">Поиск</el-button>
                </el-input>
            </form>
        </div>
        @show
        <div class="el-width-auto" style="min-width: 36px;">
            <i class="el-icon-loading el-icon-2x" v-show="state.isLoading"></i>
        </div>
        <div class="el-width-auto">
            <div class="el-badge">
                <a href="#" class="el-text-muted"><i class="el-icon-bell3"></i></a>
                <sup class="el-badge__content el-badge__content--undefined is-fixed">2</sup>
            </div>
        </div>
        <div class="el-width-auto el-height-1-1">
            <el-dropdown class="el-height-1-1" trigger="click">
                <span class="el-dropdown-link el-height-1-1 el-display-block account-dropdown-card">
                    <div class="el-grid el-grid-collapse el-flex el-flex-middle el-height-1-1">
                        @if ($user)
                        <div class="el-width-expand el-text-right">
                            <div class="el-text-bold">{{ $user->name }}</div>
                            <div class="el-text-muted el-text-sm" style="margin-top: 6px;">{{ $user->email }}</div>
                        </div>
                        @endif
                        <div class="el-width-auto">
                            <i class="el-icon-arrow-down12" style="padding: 0 5px;"></i>
                        </div>
                        <div class="el-width-auto">
                            <span class="el-avatar el-avatar--circle" style="height: 40px; width: 40px; line-height: 40px;">
                                <img src="{{ $user->avatar }}" style="object-fit: cover;">
                            </span>
                        </div>
                    </div>
                </span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item icon="el-icon-user">Мой профиль</el-dropdown-item>
                    <el-dropdown-item>
                        <a href="{{ route('dashboard.settings') }}"><i class="el-icon-equalizer"></i>Настройки</a>
                    </el-dropdown-item>
                    <li tabindex="-1" class="el-dropdown-menu__item el-dropdown-menu__item--divided">
                        <a href="{{ route('dashboard.logout') }}"><i class="el-icon-exit"></i>Выйти</a>
                    </li>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
    </nav>
</header>
