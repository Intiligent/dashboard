<?php

namespace Dashboard\Http\Controllers;

use DB;
use Meta;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * View permission list
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $collection = Permission::query()
            ->withCount(['users', 'roles'])
            ->applyFilter($request)
            ->orderBy('guard_name', 'DESC')
            ->paginate()
            ->withQueryString();

        Meta::set('title', 'Permission list');
        SharedData::put([
            'collection' => $collection,
        ]);
        return view('dashboard::pages.acl.permission-list', [
            'collection' => $collection,
        ]);
    }

    /**
     * View create/update permission
     *
     * @param integer|string $permissionId
     * @return Illuminate\View\View
     */
    public function item($permissionId): View
    {
        $permission = Permission::findOrNew($permissionId);
        $permission->assign_roles = $permission->roles->pluck('id')->toArray();
        $guards = array_keys(config('auth.guards'));
        $roles = Role::all();
        Meta::set('title', 'Permission: ' . ($permission->name ?? 'new'));
        SharedData::put([
            'permission' => $permission,
            'guards' => $guards,
        ]);
        return view('dashboard::pages.acl.permission-item', [
            'permission' => $permission,
            'guards' => $guards,
            'roles' => $roles,
        ]);
    }

    /**
     * Create/update permission
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function postPermission(Request $request): array
    {
        $request->user()->hasPermissionTo('permission.post', 'dashboard');
        $payload = $this->validate($request, [
            'name' => ['required', 'min:2'],
            'guard_name' => ['required'],
            'description' => ['sometimes'],
            'assign_roles' => ['sometimes', 'array'], // , 'required_if:id'
        ]);
        $permission = Permission::updateOrCreate([
            'id' => $request->id ?: null],
            Arr::only($payload, ['name', 'guard_name', 'description'])
        );
        $permission->syncRoles($payload['assign_roles']);
        if ($permission->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => 'Permission succesful create',
            ]);
        }
        return $this->response([MSG => 'Permission succesful update']);
    }

    /**
     * Permission delete
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function deletePermission(Request $request): array
    {
        $this->validate($request, [
            'id' => ['required'],
        ]);
        $destroy = Permission::destroy($request->id);
        return $this->response([MSG => 'Permission success delete']);
    }
}
