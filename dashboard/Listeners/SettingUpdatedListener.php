<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\SettingUpdatedEvent;
use Dashboard\Models\Setting;
use Throwable;

class SettingUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param SettingDeleted $event
     * @return void
     */
    public function handle(SettingUpdatedEvent $event)
    {
        $settings = Setting::whereNotNull('parent_id')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
        $file = base_path('dashboard/config/pulse.php');
        $content = '<?php '.PHP_EOL.'return '.var_export($settings, true).';';

        try {
            File::put($file, $content);
        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'No such file or directory')) {
                File::makeDirectory(base_path('dashboard/config/'));
            }
            File::put($file, $content);
        } finally {
            Artisan::call('config:cache');
        }
    }
}
