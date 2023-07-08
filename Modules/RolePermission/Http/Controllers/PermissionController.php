<?php

namespace Modules\RolePermission\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\Entities\Permission;
use Modules\RolePermission\Entities\Role;
use Toastr;
use Validator;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {

        try {
            $role_id = $request['id'];
            if ($role_id == null || $role_id == 1) {
                return redirect(route('permission.roles.index'));
            }
            $app_ids = [3000, 3001, 3002, 3003, 3004, 3005, 3006, 3007, 3008, 3009];
            $client_login_ids = [3100, 3101, 3102, 3103, 3104, 3105, 3106, 3107, 3108, 3109, 3110, 3111, 3112, 3113, 3114, 3115, 3116, 3117, 3118, 3119, 3200];

            $PermissionList = Permission::when(!moduleStatusCheck('Appointment'), function ($query) use ($app_ids) {
                $query->whereNotIn('id', $app_ids);
            })->when(!moduleStatusCheck('ClientLogin'), function ($query) use ($client_login_ids) {
                $query->whereNotIn('id', $client_login_ids);
            })->when(moduleStatusCheck('AdvSaas'), function ($query) {
                $saas_ids = [605, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 602, 384, 385, 386, 387, 388, 389, 390, 500, 606];
                $query->whereNotIn('id', $saas_ids);
            })->get();
            $role = Role::with('permissions')->find($role_id);
            $data['role'] = $role;
            $data['MainMenuList'] = $PermissionList->where('type', 1);
            $data['SubMenuList'] = $PermissionList->where('type', 2);
            $data['ActionList'] = $PermissionList->where('type', 3);
            $data['PermissionList'] = $PermissionList;
            return view('rolepermission::permission', $data);

        } catch (\Exception $e) {
            Toastr::error(__("common.Something Went Wrong"));
            return back();
        }

    }

    public function store(Request $request)
    {
        $validate_rules = [
            'role_id' => "required",
            'module_id' => "required|array",
        ];

        $validator = Validator::make($request->all(), $validate_rules, validationMessage($validate_rules));

        if ($validator->fails()) {
            Toastr::error('Validation Failed', __('common.Failed'));
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            $role = Role::findOrFail($request->role_id);
            $role->permissions()->where('organization_id', auth()->user()->organization_id)->detach();
            $role->permissions()->attach(array_unique($request->module_id), [
                'organization_id' => auth()->user()->organization_id
            ]);
            DB::commit();

            Toastr::success('Operation successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {

            DB::rollback();
            Toastr::error(__("common.Something Went Wrong"), __('common.Failed'));
            return redirect()->back();
        }
    }
}
