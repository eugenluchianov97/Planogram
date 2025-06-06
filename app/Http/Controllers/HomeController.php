<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        return redirect('/dashboard');
        // $shops = Shop::query();
        // if (auth()->user()->role == 'user') {
        //     $shops->where('id', auth()->user()->shop_id);
        // } else if (auth()->user()->role == 'manager') {
        //     $shops = $shops->whereIn('id', collect(auth()->user()->shops)->map(function ($shop) {return $shop->id;}));
        // }

        // return inertia('Home', [
        //     'shops' => $shops->latest('created_at')->get()
        // ]);
    }
}
