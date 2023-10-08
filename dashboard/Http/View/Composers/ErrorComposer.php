<?php

namespace Dashboard\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Coderello\SharedData\Facades\SharedData;

class ErrorComposer
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
        SharedData::put([
            'config' => config('pulse'),
            'csrf' => csrf_token(),
            'user' => auth('dashboard')->user(),
        ]);
    }
}
