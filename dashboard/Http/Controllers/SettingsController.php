<?php

namespace Dashboard\Http\Controllers;

use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Dashboard\Http\Requests\PutSettingsRequest;
use Dashboard\Http\Requests\PostSettingRequest;
use Dashboard\Http\Requests\PostSettingFileRequest;
use Dashboard\Events\SettingDeletedEvent;
use Dashboard\Events\SettingUpdatedEvent;
use Dashboard\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Settings index page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index()
    {
        // Meta::set('title', 'Settings');
        // Meta::set('description', 'Creating and editing custom settings');
        $settings = $this->getSettings();
        SharedData::put([
            'settings' => $settings,
        ]);
        return view('dashboard::pages.settings');
    }

    /**
     * Fetch settings tree items
     *
     * @return Illuminate\Illuminate\Support\Collection
     */
    public function getSettings()
    {
        return Setting::where('active', true)
            ->defaultOrder()
            ->get()
            ->toTree();
    }

    /**
     * Modify settings
     *
     * @param PostSettingRequest $request
     * @return array
     */
    public function postSetting(PostSettingRequest $request)
    {
        $payload = $request->validated();
        $model = Setting::updateOrCreate(['id' => $request->get('id')], $payload);
        if ($model->wasRecentlyCreated) {
            if ($request->filled('parent_id')) {
                $parent = Setting::where('id', $request->get('parent_id'))->first();
                $parent->appendNode($model);
            }
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $model->getAttributes(),
                MSG => __('dashboard::base.create'),
            ]);
        }
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    public function postSettingUploadFile(PostSettingFileRequest $request)
    {
        $directory = 'settings';
        $model = Setting::findOrFail($request->id);
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $basename = Str::slug($model->key).'.'.$extension;
        // $basename = $file->getClientOriginalName();
        $directoryPath = 'storage'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR;
        // search the destination folder for the file name.
        // if it already exists, add a unique hash (-s4d6de543)
        // if (file_exists($directoryPath . $basename)) {
        //     $pathinfo = pathinfo($basename);
        //     $basename = $pathinfo['filename'] . '-' . uniqid('') . '.' . $pathinfo['extension'];
        // }
        $image = Image::make($file->path());
        // $image->resize(100, 100, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0777, true, true);
        }
        $storedFileName = $directoryPath.$basename;
        $image->save($storedFileName);
        $pathFileName = '/'.$storedFileName;
        Setting::whereId($request->get('id'))->update([
            'value' => $pathFileName,
        ]);
        return $this->response([
            ERR => Response::HTTP_CREATED,
            MSG => 'Success upload file',
            DATA => [
                'path' => $pathFileName . '?v=' . uniqid(''),
            ],
        ]);
    }

    /**
     * Store settings value
     *
     * @param PutSettingsRequest $request
     * @return array
     */
    public function putSettings(PutSettingsRequest $request): array
    {
        $payload = $request->validated();
        Setting::bulkUpdate($payload, ['id'], ['value']);
        event(new SettingUpdatedEvent());
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Delete setting
     *
     * @param Request $request
     * @return array
     */
    public function deleteSetting(Request $request): array
    {
        $this->validate($request, [
            'id' => ['required', 'numeric'],
        ]);
        $model = Setting::findOrFail($request->id);
        $this->authorize('delete', [Setting::class, $model]);
        $model->destroy($request->id);
        event(new SettingDeletedEvent($model));
        return $this->response([
            MSG => __('dashboard::base.delete'),
        ]);
    }

    public function deleteSettingUploadFile(Request $request): array
    {
        $this->validate($request, [
            'id' => ['required', 'numeric'],
        ]);
        $model = Setting::findOrFail($request->id);
        $model->update(['value' => null]);
        event(new SettingDeletedEvent($model));
        return $this->response([
            MSG => __('dashboard::base.delete'),
        ]);
    }
}
