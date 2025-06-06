<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShelvesProductsRequest;
use App\Models\Barcode;
use App\Models\Product;
use App\Models\Shelves;
use App\Models\Showcase;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $products = Product::with('shop')->with('barcodes');

        if($request->has('q')){
            $products = $products->where('Name', 'like', '%' . $request->q . '%');
        }


        return inertia('Dashboard/Product/Index', [
            'products' => $products->paginate(15),
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
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'BoxWidth' => 'required|numeric|min:1',
            'BoxDepth' => 'required|numeric|min:1',
            'BoxHeight' => 'required|numeric|min:1',
        ],
            [
                '*.numeric' => 'Значение должно быть числом!',
                '*.min' => 'Минимальное значение 1!',
            ]);

        $product->BoxWidth = $validatedData['BoxWidth'];
        $product->BoxDepth = $validatedData['BoxDepth'];
        $product->BoxHeight = $validatedData['BoxHeight'];

        $product->UpdatedManually = true;
        $product->EmptyDimensions = false;

        $product->save();

        if ($request->hasFile('file')) {
            $product->setImages($request->file('file'));
        }

        return response()->json('success');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function storeShelvesProducts (Showcase $showcase, ShelvesProductsRequest $shelvesProductsRequest) {
        foreach ($shelvesProductsRequest->shelves as $item) {
            $shelves = Shelves::find($item['id']);
            $data = [];
            $shelves->products()->sync([]);
            foreach ($item['products'] as $product) {
                // $data[$product['id']] = ['deg' => $product['deg']];
                Product::where('id', $product['id'])->update([
                    'BoxWidth' => $product['BoxWidth'],
                    'BoxHeight' => $product['BoxHeight'],
                    'BoxDepth' => $product['BoxDepth'],
                ]);
                $shelves->products()->attach([$product['id'] => ['deg' => $product['deg']]]);
                dump($product['deg']);
            }
        }

        return response()->json([
            'message' => 'Данные витрины успешно обновлены'
        ]);
    }
}
