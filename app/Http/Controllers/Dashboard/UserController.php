<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\AdminEditRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Shelves;
use App\Models\Shop;
use App\Models\Showcase;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        if ($request->has('source') && $request->source == 'showcases') {
            return response()->json([
                'showcases' => Showcase::whereShopId($request->shop_id)->get(),
            ]);
        }

        $admins = User::query()->where('role', 'user');
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

        return Inertia::render('Dashboard/User/Index', [
            'users' => $admins->paginate(15),
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
        return Inertia::render('Dashboard/User/Create', [
            'shops' => Shop::select('id', 'name')->latest('created_at')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $user = User::create($request->validated() + [
            'role' => 'user'
        ]);

        $user->showcases()->attach(collect($request->showcases)->map(function ($a) { return $a['id'];}));

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
    public function edit(User $user)
    {
        if (auth()->user()->role != 'admin' || $user->role != 'user') {
            abort(404);
        }

        $user->load('showcases');
        return Inertia::render('Dashboard/User/Edit', [
            "admin" => $user,
            'shops' => Shop::select('id', 'name')->latest('created_at')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
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

        $user->update($data + [
            'role' => 'user'
        ]);

        $user->showcases()->sync(collect($request->showcases)->map(function ($a) { return $a['id'];}));

        return response()->json('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $user->delete();
        return response()->json('success');
    }
}
