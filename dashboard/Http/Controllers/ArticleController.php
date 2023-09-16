<?php

namespace Dashboard\Http\Controllers;

// use Meta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
// use Dashboard\Events\ArticleUpdated;
use Dashboard\Rules\Slug;
use App\Models\Article;
use App\Models\Language;

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
        $collection = $this->getArticles($request);
        // Meta::set('title', 'Article page');
        SharedData::put([
            'collection' => $collection[DATA],
        ]);
        return view('dashboard::pages.article.list');
    }

    /**
     * Show article page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function showArticlePage($id)
    {
        $article = Article::findOrNew($id);
        $languages = Language::where('active', true)->get();
        // Meta::set('title', 'Article - ' . ($article->title ?: 'новая'));
        SharedData::put([
            'model' => $article,
            'languages' => $languages,
        ]);
        return view('dashboard::pages.article.item');
    }

    /**
     * Fetch article list
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getArticles(Request $request)
    {
        $collection = Article::query()
            ->select('id', 'title', 'slug', 'text', 'active', 'created_at')
            // ->applySort($request)
            ->paginate();

        return $this->response([DATA => $collection]);
    }

    /**
     * Modify article item
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postArticle(Request $request)
    {
        $payload = $this->validate($request, [
            'lang_id' => ['required'],
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
        $model = Article::updateOrCreate(['id' => $request->get('id') ?: null], $payload);
        if ($model->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => __('dashboard::base.create'),
            ]);
        }
        // event(new ArticleUpdated($model));
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Copy article
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
            MSG => __('dashboard::base.create'),
            DATA => $copy,
        ]);
    }

    /**
     * Delete article
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
            MSG => __('dashboard::base.delete'),
        ]);
    }
}
