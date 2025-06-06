<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopRequest;
use App\Jobs\ProductIntegrationJob;
use App\Models\ArchivesIntegrations;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductIntegration;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::query();
        if (auth()->user()->role == 'user') {
            $shops->where('id', auth()->user()->shop_id);
        } else if (auth()->user()->role == 'manager') {
            $shops->whereIn('id', collect(auth()->user()->shops)->map(function ($shop) {return $shop->id;}));
        }

        return inertia('Dashboard/Shop/Index', [
            'shops' => $shops->latest('created_at')->paginate(),
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
    public function store(ShopRequest $request)
    {
        Shop::create($request->validated());
        
        return response()->json([
            'success' => "Торговая точка успешно создан"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $shop = Shop::with('showcases.shelves.products.barcodes')->findOrFail($id);
        $groups = Group::where('Parent_Key', null)->with('allChildren')->get();


        return inertia('Dashboard/Shop/Planogram', [
            'shop' => $shop,
            'shops' => Shop::select('id', 'name')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'producers' => Producer::orderBy('name')->get(),
            'userType' => auth()->user()->role,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopRequest $request, Shop $shop)
    {
        $shop->update($request->validated());
        
        return response()->json([
            'success' => "Торговая точка успешно обновлен"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();
        
        return response()->json([
            'success' => "Торговая точка успешно удален"
        ]);
    }


    public function updateProducts ($id) {
        $shop = Shop::where('c_id', $id)->firstOrFail();
        $productUpdated = ProductIntegration::where('shop_id', operator: $shop->id)->where('last_updated_at', '>=', Carbon::now()->startOfDay())->exists();
        if (!$productUpdated) {
            $productIntegration = ProductIntegration::where('shop_id',  $shop->id)->first();
            $productIntegration->update([
                'last_updated_at' => Carbon::now()
            ]);

            ProductIntegrationJob::dispatch($id);

            return response()->json([
                'message' => "Обновление товаров успешно добавлено в очередь"
            ]);
        }
        return response()->json([
            'message' => "Обновление товаров уже добавлен в очередь"
        ]);
    }

    public function getProducts(Request $request, $id) {

        $products =  Product::whereShopId($id)
            ->with('barcodes')
            ->where('BoxWidth', '>', 0)
            ->where('BoxHeight', '>', 0);


            if (!empty($request->search)) {
                $products->where('Name', 'LIKE', "%".$request->search."%");
            }

            if (!empty($request->selectedCategory)) {
                $products->where('category_id', $request->selectedCategory);
            }

            if (!empty($request->producer_id)) {
                $products->where('producer_id', $request->producer_id);
            }

            if (!empty($request->selectedGroup)) {
                $groups = Group::where('Key', $request->selectedGroup)->with('allChildren')->get();

                $arrGroups = Group::recursion($groups);

                $products->whereIn('GroupKey',$arrGroups);
            }

//            if (!empty($request->year) && !empty($request->month)) {
//
//                $products->with(['archives' => function($query) use ($request){
//                    $query->where('year',$request->year)->where('month',$request->month)->first();
//                }]);
//            }


        $sortProducts = [];

            if($products->count() < 501)  {
                $sortProducts = $products->get() ;
            }

        return response()->json([
            'sortProducts' => $sortProducts,
            'products' => $products->paginate(30),

        ]);
    }

    public function getSubgroup($group_key) {

        $subGroups = Group::where('Parent_Key',$group_key)->get();

        return response()->json([
            'subGroups' => $subGroups,
        ]);
    }
}
