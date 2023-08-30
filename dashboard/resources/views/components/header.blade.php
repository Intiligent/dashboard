<nav class="el-grid el-flex el-flex-middle" style="height: 70px;">
    <div class="">
        <el-button size="large" type="primary" class="is-narrow" plain>
            <i class="el-icon-menu7"></i>
        </el-button>
    </div>
    <div class="el-width-expand">
        <form method="POST" @submit.prevet="">
            <el-input
                size="large"
                v-model="searchQuery"
                placeholder="Type some text to search"
                clearable
            >
                <template #prefix>
                    <i class="el-icon-search4"></i>
                </template>
                <template #append>
                    <el-button native-type="submit">Search</el-button>
                </template>
            </el-input>
        </form>
    </div>
    <div class="" style="min-width: 18px;">
        <span class="spinner-icon"></span>
    </div>
    <div class="">
        <!-- @command="handleCommand" -->
        <el-dropdown
            trigger="click"
            placement="bottom-end"
        >
            <div class="el-dropdown-link">
                <div class="el-grid el-grid-xs el-flex-middle">
                    <div class="el-text-right">
                        <div class="el-text--bold" style="line-height: 20px;">Vitaliy Artykh</div>
                        <div class="el-text--small el-text--placeholder" style="line-height: 18px;">vitaliy.artyukh@gmail.com</div>
                    </div>
                    <div class="">
                        <i class="el-icon-arrow-down22"></i>
                    </div>
                    <div class="">
                        <el-avatar src="https://lh3.googleusercontent.com/a/AAcHTtcsWsVUkoFjJ62PCN9WNeyc4yWl32qo2uWSfqQ9tCZncHo=s83-c-mo"/>
                    </div>
                </div>
            </div>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item command="a">Action name with long name</el-dropdown-item>
                    <el-dropdown-item command="b">My profile</el-dropdown-item>
                    <el-dropdown-item command="c">Action 3</el-dropdown-item>
                    <el-dropdown-item command="d" disabled>Action 4</el-dropdown-item>
                    <el-dropdown-item command="e" divided>
                        <i class="el-icon-switch2 el-icon--left"></i>Logout
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
    </div>
</nav>
