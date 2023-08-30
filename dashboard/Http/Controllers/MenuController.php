<?php

namespace Dashboard\Http\Controllers;

// use Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Http\Requests\PostMenuRequest;
use Dashboard\Events\MenuDeletedEvent;
use Dashboard\Events\MenuUpdatedEvent;
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
        $tree = Menu::query()
            ->defaultOrder()
            ->get()
            ->each(function($row) {
                $row->makeVisible(['is_system', 'code', 'active']);
            })
            ->toTree();
        $routes = self::getRoutCollection();
        // Meta::set('title', __('base.menu'));
        SharedData::put([
            'tree' => $tree,
            'routes' => $routes,
            'MENU_TYPE_LIST' => Menu::TYPE_LIST,
        ]);
        return view('dashboard::pages.menu');
    }

    /**
     * Create/Update menu item.
     * Change menu item activity.
     *
     * @param Dashboard\Http\Requests\PostMenuRequest $request
     * @return array Rest response
     */
    public function postMenu(PostMenuRequest $request)
    {
        $payload = $request->validated();
        $model = Menu::firstOrNew(['id' => $request->get('id')]);
        $model->fill($payload);
        $original = clone $model;
        $model->save();
        event(new MenuUpdatedEvent($model, $original));
        if ($model->wasRecentlyCreated) {
            if ($request->filled('parent_id')) {
                $parent = Menu::where('id', $request->get('parent_id'))->first();
                $parent->appendNode($model);
            }
            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $model,
                MSG => __('dashboard::base.create'),
            ]);
        }
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Modify menu group.
     * Change group activity.
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
                MSG => __('dashboard::base.create'),
            ]);
        }
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Change menu item order.
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
        event(new MenuUpdatedEvent($model));
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Delete menu item
     *
     * @param Request $request
     * @return array
     */
    public function deleteMenu(Request $request)
    {
        // todo replace to form request
        $model = Menu::findOrFail($request->get('id'));
        $this->authorize('delete', [Menu::class, $model]);
        // todo
        // $request->user()->hasPermissionTo('menu.delete', 'dashboard');
        $ancestor = $model->ancestor();
        $model->delete();
        event(new MenuDeletedEvent($ancestor ?: $model));
        return $this->response([
            MSG => __('dashboard::base.delete'),
        ]);
    }

    /**
     * Route list
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
