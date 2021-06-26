<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Events\CategoryUpdated;
use App\Models\Category;

class CatalogController extends Controller
{
    /**
     * Catalog page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function showCategoryList()
    {
        $tree = Category::withCount(['products'])->defaultOrder()->get()->toTree();
        Meta::set('title', 'Каталог');
        SharedData::put([
            'tree' => $tree,
        ]);

        return view('dashboard::pages.catalog.index');
    }

    /**
     * Сохранение/Редактирование категории
     * Изменение активности
     *
     * @param Illuminate\Http\Request $request
     * @return array Rest response
     */
    public function postCategory(Request $request)
    {
        $data = $this->validate($request, [
            'id' => ['sometimes', 'nullable', 'numeric'],
            'parent_id' => ['sometimes', 'nullable', 'numeric'],
            'name' => ['required_without:id', 'min:1'],
            'description' => ['sometimes'],
            'slug' => ['sometimes'],
            'active' => ['sometimes', 'required_with:id', 'boolean'],
        ]);

        $model = Category::updateOrCreate(['id' => $data['id']], $data);
        if ($model->wasRecentlyCreated) {
            if ($request->filled('parent_id')) {
                $parent = Category::where('id', $data['parent_id'])->first();
                $parent->appendNode($model);
            }

            return $this->response([
                ERR => Response::HTTP_CREATED,
                DATA => $model->getAttributes(),
                MSG => __('base.create'),
            ]);
        }
        // emit event
        event(new CategoryUpdated($model));

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
    public function putCategoryOrder(Request $request)
    {
        $data = $request->validate([
            'id' => ['required'],
            'parent_id' => ['sometimes'],
            'event' => ['required', 'in:before,after,inner'],
            'offset' => ['sometimes'],
        ]);
        $model = Category::findOrFail($request->get('id'));
        if ($data['event'] === 'before') {
            $model->up($request->get('offset'));
        }
        if ($data['event'] === 'after') {
            $model->down($request->get('offset'));
        }
        if ($data['event'] === 'inner') {
            $parent = Category::findOrFail($request->get('parent_id'));
            $parent->prependNode($model);
            if ($request->get('offset') > 0) {
                $model->down($request->get('offset'));
            }
        }
        // emit event
        event(new CategoryUpdated($model));

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
    public function deleteCategory(Request $request) {
        $model = Category::findOrFail($request->get('id'));
        // check media and delete if exists
        $model->delete();
        return $this->response([
            MSG => 'Category success delete',
        ]);
    }
}
