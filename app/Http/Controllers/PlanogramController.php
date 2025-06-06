<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Showcase;
use Illuminate\Http\Request;

class PlanogramController extends Controller
{
    public function __invoke($id) {

        $shop = Shop::with('showcases.shelves')->findOrFail($id);
        return inertia('Planogram', [
            'shop' => $shop,
            'shops' => Shop::select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}
