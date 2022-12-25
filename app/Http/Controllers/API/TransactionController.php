<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // Product index dan Detail
    public function all(Request $request){
      $id = $request->input('id');
      $limit = $request->input('limit', 6);
      $status = $request->input('status');

      // Parameter url
      if($id){
        $transaction = Transaction::with(['items.product'])->find($id);

        // Jika transaction ada
        if($transaction){
          return ResponseFormatter::success(
            $transaction,
            'Data transaction berhasil diambil'
          );
        }else{
          return ResponseFormatter::error(
            null,
            'Data transaction tidak ada',
            404
          );
        }
      }

      $transaction = Transaction::with(['items.product'])->where('users_id'. Auth::user());
      if($status){
        $transaction->where('status', $status);
      }

      return ResponseFormatter::success(
        $transaction->paginate($limit),
        'Data list transaction berhasil diambil'
      );
    }

    public function checkout(Request $request){
      // validasi data
      $request->validate([
        'items' => 'required|array',
        'items.*.id' => 'exists:product,id',
        'total_price' => 'required',
        'shipping_price' => 'required',
        'status' => 'required|in:PENDING,SUCCESS,CANCELLED, FAILED, SHIPPING, SHIPPED',
      ]);

      $transaction = Transaction::create([
        'users_id' => Auth::user()->id,
        'address' => $request->address,
        'total_price' => $request->total_price,
        'shipping_price' => $request->shipping_price,
        'status' => $request->status,
      ]);

      foreach($request->items as $product){
        TransactionItem::create([
          'users_id' => Auth::user()->id,
          'products_id' => $product['id'],
          'transactions_id' => $transaction->id,
          'quantity' => $product['quantity'],
        ]);
      }

      return ResponseFormatter::success(
        $transaction->load('items.product'),
        'Transaction berhasil');
    }
}
