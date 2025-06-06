<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\AdminEditRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $admins = User::query()->where('role', 'admin');
        $sortBy = $request->sort_by;
        $sortDir = $request->sort_dir;

        if (!empty($sortBy) && !empty($sortDir)) {
            if ($sortBy == 'last_updated_at') {
                $sortBy = 'updated_at';
            }
            $admins->orderBy($sortBy, $sortDir);
        } else {
            $admins->orderBy('created_at', 'desc');
        }

        return Inertia::render('Dashboard/Admin/Index', [
            'admins' => $admins->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }
        return Inertia::render('Dashboard/Admin/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        User::create($request->validated() + [
            'role' => 'admin'
        ]);

        return response()->json('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        return Inertia::render('Dashboard/Admin/Edit', [
            "admin" => $admin,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request, User $admin)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data + [
            'role' => 'admin'
        ]);

        return response()->json('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $admin->delete();
        return response()->json('success');
    }
}
