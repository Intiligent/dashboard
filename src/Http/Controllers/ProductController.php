<?php

namespace Dashboard\Http\Controllers;

use DB;
use Meta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Events\ProductUpdated;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Show product list
     *
     * @param Illuminate\Http\Request $request [{q}]
     * @return Illuminate\Support\Facades\View
     */
    public function showProductList(Request $request)
    {
        // список товаров
        $collection = Product::select('id', 'category_id', 'slug', 'name', 'price', 'active', 'rate')
            ->with(['category:id,name,slug', 'image:id,model_id,path'])
            ->applyFilter($request->only(['q', 'category']))
            ->paginate(30);
        // категории
        $categories = Category::select('id', 'name')->withDepth()->get()->toFlatTree();

        // передача данных на frontend
        SharedData::put([
            'collection' => $collection,
            'categories' => $categories,
        ]);
        Meta::set('title', 'Товары');

        return view('dashboard::pages.product.list', [
            'collection' => $collection,
        ]);
    }

    /**
     * Show product page
     *
     * @param mixed $id
     * @return Illuminate\Support\Facades\View
     */
    public function showProduct($id)
    {
        // товар
        $product = Product::with(['category', 'image'])->findOrNew($id);
        // категории
        $categories = Category::select('id', 'name')->withDepth()->defaultOrder()->get()->toFlatTree();
        // передача данных на frontend
        SharedData::put([
            'product' => $product,
            'categories' => $categories,
        ]);
        return view('dashboard::pages.product.item', [
            'product' => $product,
        ]);
    }

    /**
     * Поиск товаров
     *
     * @param Illuminate\Http\Request $request [{q}]
     * @return array
     */
    public function getProductSuggest(Request $request)
    {
        $this->validate($request, [
            'q' => ['sometimes'],
        ]);
        // список товаров
        $collection = Product::select('id', 'category_id', 'name', 'price')
            ->with(['category:id,name,slug', 'image:id,model_id,path'])
            ->where('name', 'LIKE', '%'.$request->input('q').'%')
            ->limit(10)
            ->get();

        return $this->response([
            DATA => $collection,
        ]);
    }

    /**
     * Создание/Обновление товара
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postProduct(Request $request)
    {
        $data = $this->validate($request, [
            'name' => ['sometimes', 'required', 'min:2'],
            'slug' => [
                'sometimes', 'nullable', 'min:2',
                Rule::unique('products')->ignore($request->get('id')),
            ],
            'description' => ['sometimes'],
            'category_id' => ['sometimes', 'required_without:id', 'numeric'],
            'price' => ['sometimes', 'numeric'],
            'active' => ['required_with:id', 'boolean'],
        ], [
            'name.required' => 'Укажите название товара',
            'slug.required_without' => 'Заполните поле ссылки',
            'category_id.required_without' => 'Укажите категорию товара',
            'category_id.numeric' => 'Укажите категорию товара',
        ]);
        $model = Product::updateOrCreate(['id' => $request->get('id') ?: null], $data);
        if ($model->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => 'Обьект успешно сохранен',
            ]);
        }
        event(new ProductUpdated($model));
        return $this->response([MSG => 'Товар успешно обновлен']);
    }

    /**
     * Удаление товара
     *
     * @param Request $request
     * @return array
     */
    public function deleteProduct(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'min:1'],
        ]);
        $ids = [];
        $product = Product::with(['image', 'gallery'])->findOrFail($request->get('id'));
        if ($product->image) {
            $ids[] = $product->image->id;
        }
        if ($product->gallery) {
            $ids = array_merge($ids, $product->gallery->pluck('id')->toArray());
        }
        if (count($ids)) {
            (new Media)->deleteMedia(['id' => $ids]);
        }
        $product->delete();
        return $this->response([MSG => 'Product success delete']);
    }
}
