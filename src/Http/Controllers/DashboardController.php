<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Models\SettingGroup;

class DashboardController extends Controller
{
    /**
     * Dashboard home page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index()
    {
        Meta::set('title', 'Панель управления');
        SharedData::put([
            // 'settings' => $this->getSettingsGroups(),
        ]);

        return view('dashboard::pages.dashboard');
    }
}
