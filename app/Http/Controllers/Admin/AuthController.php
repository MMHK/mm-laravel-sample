<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/7
 * Time: 12:03
 */

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

class AuthController extends BaseController
{

    public function signIn() {
        return view('admin.sign-in')
            ->with([
                'layout' => false,
            ]);
    }

    public function handleSignIn(Request $request) {
        $this->validate($request, [
            'login_id' => 'required',
            'password' => 'required'
        ]);

        if (\Auth::guard('admin')
            ->attempt($request->except('_token'))) {

            $ref = \Session::pull('ref', route('admin.dashboard'));

            return redirect($ref);
        }

        return redirect()
            ->back()
            ->withInput([
                'login_id' => $request->input('login_id'),
            ])
            ->withErrors([
                'password' => 'invalid pwd.',
            ]);
    }

    public function handleSignOut() {
        \Auth::guard('admin')->logout();

        return redirect(route('admin.sign-in'));
    }
}