<?php

namespace Dashboard\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Menu;

class NavbarComposer
{
    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('navbarMenu', Menu::getStoreMenu('dashboard'));
    }
}
