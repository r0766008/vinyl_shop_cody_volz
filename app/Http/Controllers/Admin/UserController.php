<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JsonHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_name = '%' . $request->user . '%';
        if ($request->orderBy != null) {
            $users = User::where('name', 'like', $user_name)
                ->orWhere('email', 'like', $user_name)
                ->orderBy(explode('Q', $request->orderBy)[0], explode('Q', $request->orderBy)[1])
                ->paginate(10)
                ->appends(['user' => $request->user, 'orderBy' => $request->orderBy]);
        } else {
            $users = User::orderBy('name')
                ->paginate(10);
        }
        JsonHelper::dump($users);
        $result = compact('users');
        return view('admin.users.index', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return redirect('admin/users');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (auth()->id() == $user->id) {
            session()->flash('danger', 'In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile');
            return redirect('admin/users');
        };
        $result = compact('user');
        JsonHelper::dump($result);
        return view('admin.users.edit', $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (auth()->id() == $user->id) {
            session()->flash('danger', 'In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile');
            return redirect('admin/users');
        };
        $this->validate($request, [
            'name' => 'required|min:3|unique:users,email,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->active = (isset($_POST['active'])) ? 1 : 0;
        $user->admin = (isset($_POST['admin'])) ? 1 : 0;

        $user->save();
        session()->flash('success', 'The user <b>' . $user->name . '</b> has been updated');
        return redirect('admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
            session()->flash('danger', 'In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile');
            return abort(403, 'ERROR');
        };
        $user->delete();
        session()->flash('success', 'The user <b>' . $user->name . '</b> has been deleted');
    }
}
