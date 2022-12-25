<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    // Product index dan Detail
    public function all(Request $request){
      $id = $request->input('id');
      $limit = $request->input('limit', 6);
      $name = $request->input('name');
      $show_product = $request->input('show_product');

      // Parameter url
      if($id){
        $category = ProductCategory::with(['products'])->find($id);

        // Jika product ada
        if($category){
          return ResponseFormatter::success(
            $category,
            'Data category berhasil diambil'
          );
        }else{
          return ResponseFormatter::error(
            null,
            'Data category tidak ada',
            404
          );
        }
      }

      $category = ProductCategory::query();

      // Filtering
      if($name){
        $category->where('name', 'like', '%' . $name . '%');
      }

      if($show_product){
        $category->with('products');
      }

      // Mengembalikan hasil filtering
      return ResponseFormatter::success(
        $category->paginate($limit),
        'Data list category berhasil diambil'
      );
    }
}
