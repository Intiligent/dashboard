<?php

namespace Dashboard\Http\Controllers;

// use Meta;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    /**
     * View role list
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index(): View
    {
        $collection = Role::query()
            ->withCount(['users', 'permissions'])
            ->latest('id')
            ->paginate(40);

        // Meta::set('title', 'Roles');
        SharedData::put([
            'collection' => $collection,
        ]);
        return view('dashboard::pages.role.list');
    }

    /**
     * View create/edit role
     *
     * @param integer|string $roleId
     * @return Illuminate\Support\Facades\View
     */
    public function item($roleId): View
    {
        $role = Role::findOrNew($roleId);
        $role->assign_permissions = $role->permissions->pluck('id')->toArray();
        $permissions = Permission::orderBy('guard_name', 'DESC')->get();
        // Meta::set('title', 'Role: ' . ($role->title ?? 'new'));
        SharedData::put([
            'role' => $role,
            'permissions' => $permissions,
        ]);
        return view('dashboard::pages.role.item');
    }

    /**
     * Post role with permissions
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function postRole(Request $request): array
    {
        $payload = $this->validate($request, [
            'name' => ['required'],
            'assign_permissions' => ['sometimes', 'array'],
        ]);
        $role = Role::updateOrCreate(['id' => $request->id ?: null], Arr::only($payload, ['name']));
        $role->permissions()->sync($payload['assign_permissions']);
        if ($role->wasRecentlyCreated) {
            return $this->response([
                ERR => Response::HTTP_CREATED,
                MSG => 'Role success create',
            ]);
        }
        return $this->response([
            MSG => 'Role success update',
        ]);
    }

    /**
     * Role delete
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function deleteRole(Request $request): array
    {
        $this->validate($request, [
            'id' => ['required'],
        ]);
        $destroy = Role::destroy($request->input('id'));
        if (!$destroy) {
            return $this->response([
                ERR => Response::HTTP_PRECONDITION_FAILED,
                MSG => 'Delete error',
            ]);
        }
        return $this->response([MSG => 'Role success delete']);
    }
}
