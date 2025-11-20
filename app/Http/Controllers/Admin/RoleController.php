<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\CustomPermission;
use Illuminate\Support\Facades\Auth;
use DB;
use Validator;
    
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_roles', ['only' => ['index','create','store','edit','update','destroy']]);
    }
    
    public function index(Request $request)
    {
        $roles = Role::where('is_active',1)->orderBy('id','DESC')->paginate(15);
        return view('admin.roles_permissions.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $permission = CustomPermission::whereNull('parent_id')
                        ->with(['children' => function ($q) {
                            $q->where('is_active', 1);
                        }])->where('is_active',1)->get();
        return view('admin.roles_permissions.create',compact('permission'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->givePermissionTo($request->permissions);
        
        session()->flash('success', 'Role created successfully');
        return redirect()->route('roles.index');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = CustomPermission::whereNull('parent_id')
                        ->with(['children' => function ($q) {
                            $q->where('is_active', 1);
                        }])->where('is_active',1)->get();

        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('admin.roles_permissions.edit',compact('role','permission','rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permissions'));

        session()->flash('success', 'Role updated successfully');
        return redirect()->route('roles.index');
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('status',trans('messages.role_delete_success'));
    }
}