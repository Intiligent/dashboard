<?php

namespace Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
use Intervention\Image\Facades\Image;
use Dashboard\Http\Requests\PostUserAvatarRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show user list
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\View
     */
    public function showList(Request $request): View
    {
        $collection = $this->getUsers($request);
        // Meta::set('title', __('base.users'));
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
    public function showItem($id): View
    {
        $user = User::findOrNew($id);
        $user->makeVisible(['created_at', 'updated_at']);
        // $user->assign_roles = $user->roles->pluck('id')->toArray();
        // $roles = Role::all();
        SharedData::put([
            'model' => $user,
        ]);
        return view('dashboard::pages.user.item');
    }

    /**
     * Fetch user list
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getUsers(Request $request): array
    {
        sleep(2);
        $collection = User::query()
            // ->with('roles:id,name')
            ->select('id', 'name', 'email', 'avatar', 'created_at')
            ->applySort($request)
            ->applyFilter($request)
            ->paginate(30);

        $collection->makeVisible(['created_at']);

        return $this->response([
            DATA => $collection,
        ]);
    }

    /**
     * Create/update user
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postUser(Request $request): array
    {
        // todo form request
        $payload = $this->validate($request, [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email:rfc'],
            // 'assign_roles' => ['sometimes', 'array'],
            'password' => ['sometimes', 'nullable', 'required_without:id', 'min:6'],
        ]);
        $user = User::updateOrCreate(['id' => $request->get('id')], $payload);
        // if ($payload['assign_roles']) {
            // $roles = Role::whereIn('id', $payload['assign_roles'])->get()->pluck('name');
            // $user->assignRole($roles, 'dashboard');
        // }
        if ($user->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => __('dashboard::base.create'),
            ]);
        }
        // event(new ArticleUpdated($user));
        return $this->response([
            MSG => __('dashboard::base.update'),
        ]);
    }

    /**
     * Upload user avatar
     *
     * @param PostUserAvatarRequest $request
     * @return array
     */
    public function postUserAvatar(PostUserAvatarRequest $request): array
    {
        $directory = 'avatar';
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $basename = substr(md5($directory.$request->get('id')), 0, 10).'.'.$extension;
        // $basename = $file->getClientOriginalName();
        $directoryPath = 'storage'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR;
        // search the destination folder for the file name.
        // if it already exists, add a unique hash (-s4d6de543)
        // if (file_exists($directoryPath . $basename)) {
        //     $pathinfo = pathinfo($basename);
        //     $basename = $pathinfo['filename'] . '-' . uniqid('') . '.' . $pathinfo['extension'];
        // }
        $image = Image::make($file->path());
        $image->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        });
        $storedFileName = $directoryPath.$basename;
        $image->save($storedFileName);
        $pathFileName = '/'.$storedFileName;
        User::whereId($request->get('id'))->update([
            'avatar' => $pathFileName,
        ]);
        return $this->response([
            MSG => 'Success upload file',
            DATA => [
                'path' => $pathFileName,
            ]
        ]);
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @return array
     */
    public function deleteArticle(Request $request): array
    {
        // add form request with policy
        // $this->validate($request, [
        //     'id' => ['required', 'exists:articles'],
        // ]);
        // Article::destroy($request->get('id'));
        // return $this->response([
        //     MSG => 'Article success delete',
        // ]);
    }
}
