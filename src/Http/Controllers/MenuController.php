<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Events\MenuDeleted;
use Dashboard\Events\MenuUpdated;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Dashboard home page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index()
    {
        $tree = Menu::query()->defaultOrder()->get()->each(function($row) {
            $row->makeVisible(['is_system', 'code', 'active']);
        })->toTree();
        $groups = Menu::select('id', 'name', 'code', 'active')
            ->whereNull('parent_id')
            ->get()
            ->each(function($group) {
                $group->makeVisible(['code'])->makeHidden(['url']);
            });
        $routes = self::getRoutCollection();

        Meta::set('title', __('base.menu'));
        // передача данных на frontend
        SharedData::put([
            'tree' => $tree,
            'groups' => $groups,
            'routes' => $routes,
        ]);

        return view('dashboard::pages.menu');
    }

    /**
     * Сохранение/Редактирование элемента меню.
     * Изменение активности меню.
     *
     * @param Illuminate\Http\Request $request
     * @return array Rest response
     */
    public function postMenu(Request $request)
    {
        $types = implode(',', Menu::TYPES);
        $payload = $this->validate($request, [
            'name' => ['required_without:id', 'min:2'],
            'parent_id' => ['sometimes', 'nullable', 'numeric'],
            'type' => ['sometimes', 'required', "in:$types"],
            'value' => ['sometimes', 'required_with:type'],
            'active' => ['sometimes', 'required_with:id', 'boolean'],
            'attribute' => ['sometimes'],
            'icon' => ['sometimes'],
        ], [
            'parent_id.numeric' => 'Выберите группу меню',
            'value.required_with' => 'Заполните значение ссылки',
        ]);
        $model = Menu::firstOrNew(['id' => $request->get('id')]);
        $model->fill($payload);
        $original = clone $model;
        $model->save();
        // $model = Menu::updateOrCreate(['id' => $request->get('id')], $payload);
        event(new MenuUpdated($model, $original));
        if ($model->wasRecentlyCreated) {
            if ($request->filled('parent_id')) {
                $parent = Menu::where('id', $request->get('parent_id'))->first();
                $parent->appendNode($model);
            }
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $model->getAttributes(),
                MSG => __('base.create'),
            ]);
        }
        return $this->response([
            MSG => __('base.update'),
        ]);
    }

    /**
     * Сохранение/Редактирование группы меню.
     * Изменение активности группы.
     *
     * @param Illuminate\Http\Request $request
     * @return array Rest response
     */
    public function postMenuGroup(Request $request)
    {
        $payload = $this->validate($request, [
            'name' => ['required_without:id', 'min:2'],
            'code' => [
                'sometimes',
                'required_with:name',
                Rule::unique('menu_items')->ignore($request->get('id')),
            ],
            'active' => ['sometimes', 'required_with:id', 'boolean'],
        ]);
        $group = Menu::updateOrCreate(['id' => $request->get('id')], $payload);
        if ($group->wasRecentlyCreated) {
            $group->makeVisible(['code'])->makeHidden(['url', 'parent_id']);
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $group,
                MSG => __('base.create'),
            ]);
        }
        return $this->response([
            MSG => __('base.update'),
        ]);
    }

    /**
     * Изменение позиции элемента меню.
     *
     * @param Illuminate\Http\Request $request
     * @return array Rest response
     */
    public function putMenuOrder(Request $request)
    {
        $data = $request->validate([
            'id' => ['required'],
            'parent_id' => ['sometimes'],
            'event' => ['required', 'in:before,after,inner'],
            'offset' => ['sometimes'],
        ]);
        $model = Menu::findOrFail($request->get('id'));
        if ($data['event'] === 'before') {
            $model->up($request->get('offset'));
        }
        if ($data['event'] === 'after') {
            $model->down($request->get('offset'));
        }
        if ($data['event'] === 'inner') {
            $parent = Menu::findOrFail($request->get('parent_id'));
            $parent->prependNode($model);
            if ($request->get('offset') > 0) {
                $model->down($request->get('offset'));
            }
        }
        event(new MenuUpdated($model));

        return $this->response([
            MSG => 'Позиция обновлена',
        ]);
    }

    /**
     * Удаление элемента меню
     *
     * @param Request $request
     * @return array
     */
    public function deleteMenu(Request $request)
    {
        $model = Menu::findOrFail($request->get('id'));
        $this->authorize('delete', [Menu::class, $model]);
        $ancestor = $model->ancestor();
        $model->delete();
        event(new MenuDeleted($ancestor ?: $model));
        return $this->response([
            MSG => 'Menu success delete',
        ]);
    }

    /**
     * Список роутов
     *
     * @return Illuminate\Support\Collection
     */
    private static function getRoutCollection()
    {
        return collect(Route::getRoutes())->filter(function($route) {
            if (Str::contains($route->uri(), '{')) {
                return false;
            }
            if (Str::startsWith($route->getName(), 'face.')) {
                return true;
            }
            if (Str::startsWith($route->getName(), 'dashboard.')) {
                return true;
            }
            return false;
        })->map(function ($route) {
            return [
                'name' => $route->getName(),
                'uri' => Str::start($route->uri(), '/'),
            ];
        })->values();
    }
}
