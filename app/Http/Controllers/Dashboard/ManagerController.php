<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\AdminEditRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerRequest;
use App\Http\Requests\ManagerUpdateRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Shelves;
use App\Models\Shop;
use App\Models\Showcase;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $admins = User::query()->where('role', 'manager');
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

        return Inertia::render('Dashboard/Manager/Index', [
            'managers' => $admins->paginate(15),
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
        return Inertia::render('Dashboard/Manager/Create', [
            'shops' => Shop::select('id', 'name')->latest('created_at')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManagerRequest $request)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $user = User::create($request->validated() + [
            'role' => 'manager'
        ]);

        $user->shops()->attach(collect($request->shops)->map(function ($a) { return $a['id'];}));

        return response()->json('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $manager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $manager)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }
        $manager->load('shops');
        return Inertia::render('Dashboard/Manager/Edit', [
            "admin" => $manager,
            'shops' => Shop::select('id', 'name')->latest('created_at')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManagerUpdateRequest $request, User $manager)
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

        $manager->update($data + [
            'role' => 'manager'
        ]);

        $manager->shops()->sync(collect($request->shops)->map(function ($a) { return $a['id'];}));

        return response()->json('success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $manager)
    {
        if (auth()->user()->role != 'admin') {
            abort(404);
        }

        $manager->delete();
        return response()->json('success');
    }
}
