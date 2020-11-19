<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Models\Admin\User;
use App\Models\SMSLog;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        \View::share([
            'page_title' => 'Admin User'
        ]);

    }

    public function index()
    {
        $list = User::paginate(10);

        return view('admin.user.list')->with(compact('list'));
    }

    public function edit(Request $request, $uid = 0)
    {
        $user      = User::find($uid);
        $routeName = $request->route()->getName();

        if ($routeName == 'admin.me') {
            /**
             * @var $user \App\Models\Admin\User
             */
            $user = \Auth::guard('admin')->user();
            $user = User::find($user->id);
        } else {
            if ($uid == 0) {
                $user = new User();
            }
        }

        if ($user) {
            return view('admin.user.edit')
                ->with(compact('user'));
        }

        throw new ApiException('user not found.');
    }

    public function handleSave(Request $request, $uid = 0)
    {
        $routeName  = $request->route()->getName();
        $in_me_flag = (in_array($routeName, ['admin.me', 'admin.me.save']));

        $new_flag = ($uid == 0);

        $this->validate($request, [
            'username'   => 'required',
            'login_id'   => 'unique:admin,login_id,' . $uid . ',login_id' . (($in_me_flag || !$new_flag) ? '' : '|required'),
            'password'   => 'required_with:login_id',
            'c_password' => 'required_with:password|same:password',
        ]);

        $pass     = $request->input('password');
        $login_id = $request->input('login_id');

        $user = User::find($uid);

        if ($in_me_flag) {
            $user = \Auth::guard('admin')->user();
        } else {
            if ($uid == 0) {
                $user = new User();
            }
        }

        if (!$user) {
            throw new ApiException('user not found.');
        }

        $user->fill([
            'username' => $request->input('username'),
        ]);

        /**
         * 判定拥有system权限
         * @var $me \App\Models\Admin\User
         */
        $me = \Auth::guard('admin')->user();
        if ($me->group) {
            $service = app('\App\Services\PermissionService');
            if ($service->haveRole($me->group, \App\Services\PermissionService::ROLE_SYSTEM)) {
                $user->fill([
                    'group' => $request->input('group'),
                ]);
            }
        }

        if ($login_id) {
            $user->fill([
                'login_id' => $login_id,
            ]);
        }

        if ($pass) {
            $user->createPwd($pass);
        }

        if ($user->save()) {
            if ($in_me_flag) {
                return redirect()->back();
            }

            return redirect(route('admin.user.index'));
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors([
                'username' => 'Save error',
            ]);
    }

    public function remove($uid)
    {
        User::where('id', $uid)
            ->delete();

        return redirect()->back();
    }

    public function adminGroup(PermissionService $service)
    {
        \View::share([
            'page_title' => '管理組'
        ]);

        $list = $service->listAdminGroup();


        $list = collect($list)->map(function ($row) {
            return [
                'name' => $row,
            ];
        });

        return view('admin.user.group', compact('list'));
    }

    public function groupRole(Request $request, PermissionService $service)
    {
        \View::share([
            'page_title' => '管理組權限'
        ]);

        $groupAlisa = $request->input('group');
        $role       = $service->getGroupRole($groupAlisa);
        $roleList   = $service->getRoleList();


        return view('admin.user.group-role', compact('roleList', 'role', 'groupAlisa'));
    }

    public function groupRoleAssign(Request $request, PermissionService $service)
    {
        $this->validate($request, [
            'group' => 'required',
        ]);

        $groupAlisa = $request->input('group');
        $role       = $request->input('role', []);

        $service->groupAssignRole($groupAlisa, $role);

        return redirect()->back();
    }


    public function listSMSLog(Request $request)
    {
        \View::share([
            'page_title' => '短信記錄列表'
        ]);

        $list = SMSLog::query()
            ->when($s = $request->input('s'), function ($query) use ($s) {
                $query->where('to_phone', 'like', '%' . $s . '%');
            })
            ->orderByDesc('created_at')
            ->paginate();

        return view('admin.user.sms-list', compact('list'));
    }
}
