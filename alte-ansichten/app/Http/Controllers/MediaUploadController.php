<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $path = $request->file('file')->store('media-items', 'public');

        return response()->json(['path' => $path]);
    }
}
