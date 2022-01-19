<?php

namespace Dashboard\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Dashboard\Events\ArticleUpdated;
use App\Models\Article;

class ArticleUpdatedHook
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
     * @param ArticleUpdated $event
     * @return void
     */
    public function handle(ArticleUpdated $event)
    {
        $this->updateArticle($event->article);
    }

    /**
     * Update article into cache
     *
     * @param string $slug [url identifier]
     * @return Article
     */
    public function updateArticle(Article $article)
    {
        return Cache::put('article-page-' . $article->slug, $article);
    }
}
