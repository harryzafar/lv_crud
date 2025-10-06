<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        // fixed compact syntax
        return view('admin.userlist', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.createuser');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4'
        ]);

        try {
            User::create([
                'name' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'roles_is' => 1
            ]);

            return redirect()->route('home')->with('message', 'User Created Successfully');
        } catch (Exception $e) {
            return back()->with('message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('message', 'User not found');
        }
        return view('admin.showuser', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    { 
        $user = User::find($id);
        if (!$user) {
            return back()->with('message', 'User not found');
        }
        return view('admin.createuser', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('message', 'User not found');
        }

        $data = $request->validate([
            'username' => 'required|string|min:4',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:4'
        ]);

        try {
            $user->name = $data['username'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            return redirect()->route('home')->with('message', 'User Updated Successfully');
        } catch (Exception $e) {
            return back()->with('message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->with('message', 'User not found');
        }

        try {
            $user->delete();
            return redirect()->route('home')->with('message', 'User Deleted Successfully');
        } catch (Exception $e) {
            return back()->with('message', 'Error: ' . $e->getMessage());
        }
    }
}
