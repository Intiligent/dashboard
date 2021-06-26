<?php

namespace Dashboard\Http\Controllers;

use Auth;
use Meta;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Dashboard\Models\SettingGroup;
use Dashboard\Models\Setting;
use Dashboard\Events\SettingDeleted;
use Dashboard\Events\SettingGroupUpdated;
use Dashboard\Models\Component;

class SettingsController extends Controller
{
    /**
     * Dashboard home page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index()
    {
        Meta::set('title', 'Настройки');
        Meta::set('description', 'Создание и редактирование пользовательских настроек');
        $settings = $this->getSettingsGroups();
        $components = Component::getComponents();
        SharedData::put([
            'settings' => $settings,
            'components' => $components,
        ]);

        return view('dashboard::pages.settings');
    }

    /**
     * получить список настроек по групам
     *
     * @return Illuminate\Illuminate\Support\Collection
     */
    public function getSettingsGroups()
    {
        $groups = SettingGroup::orderBy('order', 'ASC')
            ->with(['settings' => function($query) {
                $query->orderBy('order', 'ASC');
            }])
            ->get();

        return $groups;
    }

    /**
     * Сохранение/обновлении настройки
     *
     * @param Request $request
     * @return array
     */
    public function postSetting(Request $request)
    {
        $availableComponents = Arr::pluck(Component::getComponents(), 'name');
        $payload = $this->validate($request, [
            'key' => ['required', 'min:2', Rule::unique('settings_items')->ignore($request->get('id'))],
            'name' => ['required', 'min:2'],
            'description' => ['sometimes'],
            'group_id' => ['required', 'numeric'],
            'type' => ['required', Rule::in($availableComponents)],
            'icon' => ['sometimes'],
        ], [
            'key.unique' => 'Ключ :input уже существует. Укажите другое имя ключа.'
        ]);
        $model = Setting::find($request->get('id'));
        if ($model) {
            $this->authorize('put', [Setting::class, $model]);
        }
        $post = Setting::updateOrCreate(['id' => $request->get('id')], $payload);
        if ($post->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $post->getAttributes(),
                MSG => __('base.create'),
            ]);
        }
        return $this->response([
            MSG => __('base.update'),
        ]);
    }

    /**
     * Сохранение/Обновлении группы настроек
     *
     * @param Request $request
     * @return array
     */
    public function postSettingGroup(Request $request)
    {
        $payload = $this->validate($request, [
            'name' => ['required', 'min:2'],
            'description' => ['sometimes'],
            'icon' => ['sometimes'],
        ]);
        $model = SettingGroup::updateOrCreate(['id' => $request->get('id')], $payload);
        if ($model->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $model->getAttributes(),
                MSG => __('base.create'),
            ]);
        }
        return $this->response([
            MSG => __('base.update'),
            DATA => $model->getAttributes(),
        ]);
    }

    /**
     * Обновлении настроек группы
     *
     * @param Request $request
     * @return array
     */
    public function putSettings(Request $request)
    {
        $payload = $this->validate($request, [
            '*.id' => ['required', 'integer', 'min:1'],
            '*.group_id' => ['required', 'integer', 'min:1'],
            '*.name' => ['required'],
            '*.key' => ['required'],
            '*.value' => ['sometimes'],
        ]);
        Setting::upsert($payload, ['id', 'key', 'group_id'], ['value']);
        $first = head($request->all());
        $group = SettingGroup::with(['settings'])->find($first['group_id']);
        if ($group) {
            event(new SettingGroupUpdated($group));
        }
        return $this->response([
            MSG => __('base.save'),
        ]);
    }

    /**
     * Удаление настройки
     *
     * @param Request $request
     * @return array
     */
    public function deleteSetting(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric'],
        ]);
        $model = Setting::findOrFail($request->get('id'));
        $this->authorize('delete', [Setting::class, $model]);
        $model->destroy($request->id);
        event(new SettingDeleted());
        return $this->response([
            MSG => __('base.delete'),
        ]);
    }

    /**
     * Удаление групы настроек
     *
     * @param Request $request
     * @throws AccessDeniedHttpException [on delete system entity]
     * @return array
     */
    public function deleteSettingGroup(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric'],
        ]);
        $group = SettingGroup::findOrFail($request->get('id'));
        $this->authorize('delete', [SettingGroup::class, $group]);
        $group->destroy($request->get('id'));
        return $this->response([
            MSG => __('base.delete'),
        ]);
    }
}
