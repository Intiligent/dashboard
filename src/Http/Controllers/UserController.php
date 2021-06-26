<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show user list
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\View
     */
    public function showList(Request $request)
    {
        $collection = $this->getUsers($request, false);

        Meta::set('title', __('base.users'));
        SharedData::put([
            'collection' => $collection,
        ]);
        return view('dashboard::pages.user.list', [
            'collection' => $collection,
        ]);
    }

    /**
     * Show user page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function showItem($id)
    {
        $user = User::findOrNew($id);
        SharedData::put([
            'model' => $user,
        ]);
        return view('dashboard::pages.user.item', [
            'model' => $user,
        ]);
    }

    /**
     * Получить список пользователей
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getUsers(Request $request, $restResponse = true)
    {
        $collection = User::query()
            ->select('id', 'name', 'email', 'phone', 'avatar', 'created_at')
            ->applySort($request)
            ->paginate();

        if (!$restResponse) {
            return $collection;
        }
        return $this->response([DATA => $collection]);
    }

    /**
     * Создание/Обновление пользователя
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postUser(Request $request)
    {
        $payload = $this->validate($request, [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email:rfc'],
            'phone' => ['sometimes'],
            'password' => ['required_without:id', 'min:6'],
        ]);
        $model = User::updateOrCreate(['id' => $request->get('id')], $payload);
        if ($model->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => 'Обьект успешно сохранен',
            ]);
        }
        // event(new ArticleUpdated($model));

        return $this->response([MSG => 'Обьект успешно обновлен']);
    }

    /**
     * Удаление пользователя
     *
     * @param Request $request
     * @return array
     */
    public function deleteArticle(Request $request)
    {
        // $this->validate($request, [
        //     'id' => ['required', 'exists:articles'],
        // ]);
        // Article::destroy($request->get('id'));
        // return $this->response([
        //     MSG => 'Article success delete',
        // ]);
    }
}
