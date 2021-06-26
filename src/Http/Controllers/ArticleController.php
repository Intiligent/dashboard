<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
use Dashboard\Events\ArticleUpdated;
use Dashboard\Rules\Slug;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Show article list
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\View
     */
    public function showArticleList(Request $request)
    {
        $collection = $this->getArticles($request, false);

        Meta::set('title', 'Текстовые страницы');
        SharedData::put([
            'collection' => $collection,
        ]);
        return view('dashboard::pages.article.list', [
            'collection' => $collection,
        ]);
    }

    /**
     * Show article page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function showArticlePage($id)
    {
        $article = Article::findOrNew($id);
        Meta::set('title', 'Текстовая страница - ' . ($article->title ?: 'новая'));
        SharedData::put([
            'model' => $article,
        ]);
        return view('dashboard::pages.article.item', [
            'model' => $article,
        ]);
    }

    /**
     * Получить список страниц
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getArticles(Request $request, $restResponse = true)
    {
        $collection = Article::query()
            ->select('id', 'title', 'slug', 'views', 'active', 'created_at')
            ->applySort($request)
            ->paginate();

        if (!$restResponse) {
            return $collection;
        }
        return $this->response([DATA => $collection]);
    }

    /**
     * Создание/Обновление страницы
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postArticle(Request $request)
    {
        $data = $this->validate($request, [
            'title' => ['sometimes', 'required', 'min:2'],
            'slug' => [
                'sometimes',
                'required',
                'min:2',
                new Slug,
                Rule::unique('articles')->ignore($request->get('id')),
            ],
            'text' => ['sometimes'],
            'active' => ['required_with:id', 'boolean'],
        ]);
        $model = Article::updateOrCreate(['id' => $request->get('id') ?: null], $data);
        if ($model->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => 'Страница успешно сохранена',
            ]);
        }
        event(new ArticleUpdated($model));

        return $this->response([MSG => 'Обьект успешно обновлен']);
    }

    /**
     * Копирование обьекта
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postArticleDuplicate(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'min:1'],
        ]);
        $current = Article::find($request->get('id'));
        $copy = $current->replicate();
        $copy->slug = $current->slug . '_' . time();
        $copy->save();
        return $this->response([
            ERR => Response::HTTP_CREATED,
            MSG => 'Обьект успешно создан',
            DATA => $copy,
        ]);
    }

    /**
     * Удаление страницы
     *
     * @param Request $request
     * @return array
     */
    public function deleteArticle(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'exists:articles'],
        ]);
        Article::destroy($request->get('id'));
        return $this->response([
            MSG => 'Article success delete',
        ]);
    }
}
