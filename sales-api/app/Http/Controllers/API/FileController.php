<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as Image;
use Validator;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make(request()->all(), [
                            'file' => 'required|image|mimes:jpeg,png,jpg,gif'
                            // 'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = time().'_'.$image->getClientOriginalName() ;
            // $filename = $image->getClientOriginalName(). time(). '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $path = public_path('uploads/' . $filename);
            Image::make($image->getRealPath())->save($path);
            
            // Crear thumbnail
            $thumbnailPath = public_path('uploads/thumbnails/' . $filename);
            Image::make($image->getRealPath())->resize(150, 150)->save($thumbnailPath);

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'url' => url('uploads/' . $filename),
                'thumbnail_url' => url('uploads/thumbnails/' . $filename)
            ]);
        }

        return response()->json(['success' => false], 400);
    }
}
