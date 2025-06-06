<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Showcase;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $showcases = Showcase::with('shop:id,name')->withCount('shelves');
        if (auth()->user()->role == 'user') {
            $showcases->whereIn('id', collect(auth()->user()->showcases)->map(fn($showcase) => $showcase->id)->toArray());
        } else if (auth()->user()->role == 'manager') {
            $showcases->whereIn('shop_id', collect(auth()->user()->shops)->map(function ($shop) {return $shop->id;}));
        }

        return inertia('Dashboard/Showcase/Index', [
            'showcases' => $showcases->latest('created_at')->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Showcase $showcase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Showcase $showcase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Showcase $showcase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Showcase $showcase)
    {
        $showcase->delete();

        return response()->json([
            'success' => "Витрина успешно обновлен"
        ]);
    }
}
