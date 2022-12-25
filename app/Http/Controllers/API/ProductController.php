<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Product index dan Detail
    public function all(Request $request){
      $id = $request->input('id');
      $limit = $request->input('limit', 6);
      $name = $request->input('name');
      $description = $request->input('description');
      $tags = $request->input('tags');
      $categories = $request->input('categories');
      
      // Filter Data
      $price_from = $request->input('price_from');
      $price_to = $request->input('price_to');

      // Parameter url
      if($id){
        $product = Product::with(['category', 'galleries'])->find($id);

        // Jika product ada
        if($product){
          return ResponseFormatter::success(
            $product,
            'Data product berhasil diambil'
          );
        }else{
          return ResponseFormatter::error(
            null,
            'Data product tidak ada',
            404
          );
        }
      }

      $product = Product::with(['category', 'galleries']);

      // Filtering
      if($name){
        $product->where('name', 'like', '%' . $name . '%');
      }
      if($description){
        $product->where('description', 'like', '%' . $description . '%');
      }
      if($tags){
        $product->where('tags', 'like', '%' . $tags . '%');
      }

      if($price_from){
        $product->where('price', '>=', $price_from);
      }
      if($price_to){
        $product->where('price', '<=', $price_to);
      }

      if($categories){
        $product->where('categories', $categories);
      }

      // Mengembalikan hasil filtering
      return ResponseFormatter::success(
        $product->paginate($limit),
        'Data product berhasil diambil'
      );
    }
}
