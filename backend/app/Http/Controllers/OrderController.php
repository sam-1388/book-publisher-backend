<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $step = $request->query('step');

    if ($step) {

      if ($step == 1) {
        try {
          $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        session(['user_id' => $data['user_id']]);
        return ['success' => true];
      }
      if ($step == 2) {
        try {
          $data = $request->validate([
            'email' => ['nullable', 'email', 'required_without_all:phone_number,address,contacts',],
            'phone_number' => ['nullable', 'string', 'required_without_all:email,address,contacts',],
            'address' => ['nullable', 'string', 'required_without_all:email,phone_number,contacts',],
            'contacts' => ['nullable', 'string', 'required_without_all:email,phone_number,address',],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }


        session([
          'email' => $data['email'],
          'phone_number' => $data['phone_number'],
          'address' => $data['address'],
          'contacts' => $data['contacts']
        ]);

        return ['success' => true];
      }
      if ($step == 3) {
        try {
          $data = $request->validate([
            'purchase' => ['required', 'boolean'],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        session(['purchase' => $data['purchase']]);
        return ['success' => true];
      }
      if ($step == 4) {
        try {
          $data = $request->validate([
            'book_id' => ['nullable', Rule::exists('books', 'id')],
            'quantity' => ['required', 'numeric', 'min:1'],
            'comment' => ['exclude_unless:book_id,null', 'string']
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        $bookTitle = null;
        if (!empty($data['book_id'])) {
          $bookTitle = Book::find($data['book_id'])->title;
        }


        session([
          'book_id' => $data['book_id'],
          'quantity' => $data['quantity'],
          'comment' => $data['comment'],
          'book_title' => $bookTitle
        ]);

        return ['success' => true];
      }
      if ($step == 5) {
        try {
          $data = $request->validate([
            'services'=>['array','required','min:1','in_array_keys:publish,translate,print,other'],
            'services.*'=>['boolean'],
            'comment' => ['nullable', 'required_if:other:true'],
            'files' => ['required','array','min:1','max:10'], //user sends an array of files
            'files.*' => [File::types(['docx', 'pdf', 'jpg', 'png', 'jpeg', 'webp'])],
          ]);

          $paths = [];

          foreach ($data['files'] as $file) {
            $path = Storage::disk('local')->putFile('orderFiles', $file);
            $paths[] = $path;
          }
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        session([
          'print' => $data['print'],
          'publish' => $data['publish'],
          'translate' => $data['translate'],
          'other' => $data['other'],
          'comment' => $data['comment'],
          'files' => $paths
        ]);

        return ['success' => true];
      }
      if ($step == 6) {
        try {

          $data = $request->validate([
            'notes' => ['nullable', 'string'],
            'payment' => ['required', Rule::in(['cash', 'paypal', 'visa', 'master card'])]
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }


        $orderData = session()->only([
          'user_id',
          'email',
          'phone_number',
          'address',
          'contacts',
          'notes',
          'payment',
        ]);

        $itemData = session()->only([
          'files',
          'purchase',
          'publish',
          'translate',
          'other',
          'book_title',
          'book_id',
          'quantity',
          'comment'
        ]);

        $order = Order::create($orderData);
        $order->orderItems()->create($itemData);
        return ['success' => true];
      }
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Order $order)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Order $order)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Order $order)
  {
    //
  }
}
