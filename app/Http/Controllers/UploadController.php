<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\Image;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __invoke(UploadRequest $uploadRequest, $id) {
        $filePath = $uploadRequest->file('image')->store('documents');
        $filePath = "/storage/$filePath";

        $image = Image::create([
            'src' => $filePath,
            'showcase_id' => $id
        ]);

        return response()->json([
            'image' => $image
        ]);
    }
}
