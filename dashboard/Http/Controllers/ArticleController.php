<?php

namespace Dashboard\Http\Controllers;

// use Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
// use Dashboard\Events\ArticleUpdated;
use Dashboard\Http\Requests\GetArticlesRequest;
use Dashboard\Http\Requests\PostArticleRequest;
use App\Models\Article;
use App\Models\Language;

class ArticleController extends Controller
{
    /**
     * Show article list
     *
     * @param $request
     * @return Illuminate\Support\Facades\View
     */
    public function showArticleList(GetArticlesRequest $request)
    {
        $collection = $this->getArticles($request);
        $languages = Language::where('active', true)->get();
        // Meta::set('title', 'Article page');
        SharedData::put([
            'collection' => $collection[DATA],
            'languages' => $languages,
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
    public function getArticles(GetArticlesRequest $request)
    {
        $collection = Article::query()
            ->select('id', 'lang_id', 'title', 'slug', 'text', 'active', 'created_at')
            ->with('language')
            ->applyFilters($request)
            ->applySort($request)
            ->paginate();

        $collection->getCollection()->transform(function($row, $key) {
            $row->text = Str::limit(strip_tags($row->text), 200);
            return $row;
        });

        return $this->response([DATA => $collection]);
    }

    /**
     * Modify article item
     *
     * @param Dashboard\Http\Requests\PostArticleRequest $request
     * @return array
     */
    public function postArticle(PostArticleRequest $request)
    {
        $payload = $request->validated();
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
