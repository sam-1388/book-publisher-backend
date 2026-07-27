<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all();
        return ['books' => $books];
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
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required'],
                'page_count' => ['integer', 'max:1000', 'nullable'],
                'publishing_year' => [Rule::date()->format('Y')->todayOrBefore()],
                'author' => ['required'],
                'edition' => ['nullable'],
                'number_of_copies' => ['between:0,1000', 'nullable'],
                'image' => ['image', 'nullable'],
                'notes' => ['max:500', 'nullable']
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                400
            );
        } else {
                            // C:\Users\samps\AppData\Local\Temp\php6F37.tmp  
                            // images/ayNziA3OwxHSVYurJ7HidmnVGudy3kzqsjLTEhbP.png  

            Log::info($validator->safe()->file('image'));
            $url = Storage::disk('public')->putFile('/images', $validator->safe()->file('image'));
            // Str::replace($url,'\\','/');
            Log::info($url);
            $credentials = $validator->safe()->except('image');
            $credentials = ['image' => $url, ...$credentials];
            $book = Book::create($credentials);
            return response(
                ['redirect' => "books/{$book->id}"],
                200
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
