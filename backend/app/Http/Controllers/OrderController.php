<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        session(['user_id' => $data['user_id']]);
        session()->put('order', [
          'items' => [],
          'current' => []
        ]);
        return ['success' => true];
      }
      if ($step == 2) {
        try {
          $data = $request->validate([
            'email' => ['nullable', 'email', 'required_without_all:phone_number,address,contacts',],
            'phone_number' => ['nullable', 'string', 'required_without_all:email,address,contacts','regex:/\d{10}/'],
            'address' => ['nullable', 'string', 'required_without_all:email,phone_number,contacts',],
            'contacts' => ['nullable', 'string', 'required_without_all:email,phone_number,address',],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }


        session(
          [
            'email' => $data['email'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'address' => $data['address'] ?? null,
            'contacts' => $data['contacts'] ?? null
          ]
        );

        

        return ['success' => true ];
      }
      if ($step == 3) {
        try {
          $data = $request->validate([
            'purchase' => ['required', 'boolean'],
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }


        $previous = session('order.current');
        session()->put(
          'order.current',
          array_merge(
            $previous,
            ['purchase' => $data['purchase']]
          )
        );
        return ['success' => true];
      }
      if ($step == 4) {
        try {
          $data = $request->validate([
            'book_id' => ['nullable', 'integer', Rule::exists('books', 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
            'comment' => ['required_if:book_id,null', 'string', 'nullable']
          ]);
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        $bookTitle = null;
        if (!empty($data['book_id'])) {
          $bookTitle = Book::find($data['book_id'] ?? null)?->title;
        }


        $previous = session('order.current');
        session()->put(
          'order.current',
          array_merge(
            $previous,
            [
              'book_id' => $data['book_id'] ?? null,
              'quantity' => $data['quantity'],
              'comment' => $data['comment'] ?? null,
              'book_title' => $bookTitle ?? null
            ]
          )
        );

        session()->push(
          'order.items',
          session('order.current', [])
        );

        session()->put('order.current', []);

        return ['success' => true];
      }
      if ($step == 5) {
        try {
          $data = $request->validate([
            'print' => ['required', 'boolean'],
            'publish' => ['required', 'boolean'],
            'translate' => ['required', 'boolean'],
            'other' => ['required', 'boolean'],
            'comment' => ['nullable', 'string', 'required_if:other:true'],
            'files' => ['required', 'array', 'min:1', 'max:10'], //user sends an array of files
            'files.*' => [File::types(['docx', 'pdf', 'jpg', 'png', 'jpeg', 'webp'])],
          ]);

          if (
            ! $data['print'] &&
            ! $data['publish'] &&
            ! $data['translate'] &&
            ! $data['other']
          ) {
            return response(
              ['errors' => [
                'services' => [
                  'Select at least one service.',
                ],
              ]],
              422
            );
          }
          $paths = [];

          foreach ($data['files'] as $file) {
            $path = Storage::disk('local')->putFile('orderFiles', $file);
            $paths[] = $path;
          }
        } catch (ValidationException $th) {
          return response($th->errors(), 422);
        }

        $previous = session('order.current');
        session()->put(
          'order.current',
          array_merge(
            $previous,
            [
              'print' => $data['print'],
              'publish' => $data['publish'],
              'translate' => $data['translate'],
              'other' => $data['other'],
              'comment' => $data['comment'] ?? null,
              'files' => $paths
            ]
          )
        );

        session()->push(
          'order.items',
          session('order.current')
        );
        session()->put('order.current', []);
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



        $orderData = array_merge(
          session()->only([
            'user_id',
            'email',
            'phone_number',
            'address',
            'contacts',
            'notes',
            'payment',
          ]),
          $data
        );

        $orderItems = session('order.items', []);
        if (empty($orderItems)) {
          return response([
            'errors' => [
              'message' => 'Add at least one order item.',
            ]
          ], 422);
        }
        $order = DB::transaction(function () use ($orderData, $orderItems) {
          $order = Order::create($orderData);
          foreach ($orderItems as $key => $value) {
            $order->orderItems()->create($value);
          }
          return $order;
        });

        
        session()->forget([
          'user_id',
          'email',
          'phone_number',
          'address',
          'contacts',
          'order',
        ]);
        return ['success' => true, 'order_id' => $order->id];
      }
    }
  }
  public function getSessionItems(){
    return session('order.items',[]);
  }
  public function getUserId(){
    return session('user_id');
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
