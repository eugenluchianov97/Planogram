<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowcaseDuplicateRequest;
use App\Http\Requests\ShowcaseStoreRequest;
use App\Http\Requests\ShowcaseUpdateRequest;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shelves;
use App\Models\Shop;
use App\Models\Showcase;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function store($shop_id, ShowcaseStoreRequest $request)
    {
        if (auth()->user()->role === 'user') {
            abort(404);
        }

        $shop = Shop::findOrFail($shop_id);

        $showcase = Showcase::create($request->validated() + [
            'shop_id' => $shop->id,
        ]);

        $i = 1;
        foreach ($request->shelves as $key => $value) {
            Shelves::create([
                'position' => $i,
                'showcase_id' => $showcase->id,
                'distance' => $value['distance'],
                'shelf_depth' => $value['shelf_depth'],
            ]);
            $i++;
        }
        
        return response()->json([
            'success' => "Витрина успешно создан"
        ]);
    }

    /**
     * Store a new duplicate resource in storage.
     */
    public function duplicate($shop_id, ShowcaseDuplicateRequest $request)
    {
        if (auth()->user()->role === 'user') {
            abort(404);
        }

        $showcase = Showcase::create(array_merge($request->validated(), [
            'shop_id' => $shop_id,
        ]));

        foreach ($request->shelves as $key => $value) {
            $shelves = Shelves::create([
                'position' => $value['position'],
                'showcase_id' => $showcase->id,
                'distance' => $value['distance'],
                'shelf_depth' => $value['shelf_depth'],
            ]);

            $data = [];
            foreach ($value['products'] as $product) {
                $data[$product['id']] = ['deg' => $product['deg']];
            }
            $shelves->products()->sync($data);
        }

        return response()->json([
            'success' => "Витрина успешно создан"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function update($id, ShowcaseUpdateRequest $request)
    {
        if (auth()->user()->role === 'user') {
            abort(404);
        }

        $showcase = Showcase::findOrFail($id);
        $showcase->update($request->validated());

        foreach ($request->shelves as $key => $value) {
            $shelves = null;
            if (isset($value['id'])) {
                $shelves = Shelves::find($value['id']);
            }
            if (!empty($shelves)) {
                $shelves->update([
                    'position' => $value['position'],
                    'distance' => $value['distance'],
                    'shelf_depth' => $value['shelf_depth'],
                ]);
            } else {
                Shelves::create([
                    'position' => $value['position'],
                    'showcase_id' => $showcase->id,
                    'distance' => $value['distance'],
                    'shelf_depth' => $value['shelf_depth'],
                ]);
            }
        }
        $showcase->load('shelves');
        return response()->json([
            "success" => "Витрина успешно обновлен",
            "showcase" => $showcase
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (auth()->user()->role === 'user') {
            abort(404);
        }

        $showcase = Showcase::findOrFail($id);

        $showcase->delete();

        return response()->json([
            'success' => "Витрина успешно удалён"
        ]);
    }

    public function destroyShelves($id)
    {
        if (auth()->user()->role === 'user') {
            abort(404);
        }

        $shelves = Shelves::findOrFail($id);

        $shelves->delete();

        return response()->json([
            'success' => "Покла успешно удалён"
        ]);
    }

    public function showcaseComments($id) {
        return response()->json([
            'comments' => Comment::with('user')->latest('created_at')
            ->where('showcase_id', $id)
            ->get(),
        ]);
    }

    public function showcaseImages($id) {
        return response()->json([
            'images' => Image::query()
                ->latest('created_at')
                ->where('showcase_id', $id)
                ->get(),
        ]);
    }

    public function removeImageFromShowcase ($id){
        $image = Image::find($id);
        $image->delete();
        return response()->json([
            'message' => 'success'
        ]);
    } 
}
