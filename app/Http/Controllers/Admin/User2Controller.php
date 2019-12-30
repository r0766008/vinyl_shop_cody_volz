<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JsonHelper;

class User2Controller extends Controller
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
            $users2 = User::where('name', 'like', $user_name)
                ->orWhere('email', 'like', $user_name)
                ->orderBy(explode('Q', $request->orderBy)[0], explode('Q', $request->orderBy)[1])
                ->paginate(10)
                ->appends(['user' => $request->user, 'orderBy' => $request->orderBy]);
        } else {
            $users2 = User::orderBy('name')
                ->paginate(10);
        }
        JsonHelper::dump($users2);
        $result = compact('users2');
        return view('admin.users2.index', $result);
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
        //
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
            return response()->json([
                'type' => 'error',
                'text' => "In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile"
            ]);
        };
        $result = compact('user');
        JsonHelper::dump($result);
        return view('admin.users2', $result);
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
        if (auth()->id() == $request->id) {
            return response()->json([
                'type' => 'error',
                'text' => "In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile"
            ]);
        };
        $this->validate($request, [
            'name' => 'required|min:3|unique:users,email,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id
        ]);
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->active = (isset($_POST['active'])) ? 1 : 0;
        $user->admin = (isset($_POST['admin'])) ? 1 : 0;

        $user->save();
        return response()->json([
            'type' => 'success',
            'text' => "The user <b>$user->name</b> has been updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if (auth()->id() == $request->id) {
            return response()->json([
                'type' => 'error',
                'text' => "In order not to exclude yourself from (the admin section of) the application, you cannot delete your own profile"
            ]);
        };
        $user = User::find($request->id);
        $user->delete();
        return response()->json([
            'type' => 'success',
            'text' => "The user <b>$user->name</b> has been deleted"
        ]);
    }
}
