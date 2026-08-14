<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        foreach ($books as $book) {
            $book->image = url($book->image);
        }
        return ['books' => $books];
    }


    public function getType(string $type)
    {

        $temp = $this->getCorrectType($type);
        $temp
            ? $books = DB::table('books')->where('status', $temp)->get()
            : $books = Book::all();
        return $books;
    }

    private function getCorrectType(string $x)
    {
        switch ($x) {
            case 'translation':
                return 'need translation';
            case 'copyEditing':
                return 'need copyediting';
            case 'typeSetting':
                return 'need typesetting';
            case 'proofReading':
                return 'need proofReading';
            case 'printing':
                return 'ready for printing';

            default:
                return null;
        }
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
                'page_count' => ['integer', 'max:10000', 'nullable'],
                'publishing_year' => ['required', Rule::date()->format('Y')->todayOrBefore()],
                'author' => ['required'],
                'edition' => ['nullable'],
                'number_of_copies' => ['between:0,100000', 'nullable'],
                'image' => ['image', 'nullable', 'max:5120'],
                'notes' => ['max:500', 'nullable']
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                400
            );
        } else {

            if ($request->hasFile('image')) {
                $path = Storage::disk('public')->putFile('/images', $validator->safe()->file('image'));
            } else {
                $path = null;
            }


            $book = Book::create([
                ...$validator->safe()->except(['image']),
                'image' => $path
            ]);

            return response(
                ['redirect' => "/books/$book->id", 'success' => true],
                200
            );
        }
    }

    /**
     * Display
     *  the specified resource.
     */
    public function show(Book $book)
    {
        $book->image = url($book->image);
        return $book;
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required'],
                'page_count' => ['integer', 'max:10000', 'nullable'],
                'publishing_year' => ['required', Rule::date()->format('Y')->todayOrBefore()],
                'author' => ['required'],
                'edition' => ['nullable'],
                'number_of_copies' => ['between:0,100000', 'nullable'],
                'image' => ['image', 'nullable'],
                'notes' => ['max:500', 'nullable']
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                400
            );
        }

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($book->image);
            $path = Storage::disk('public')->putFile('/images', $validator->safe()->file('image'));
        } else {
            $path = null;
        }

        $book->update([
            ...$validator->safe()->except(['image']),
            'image' => $path
        ]);
        return response(
            ['redirect' => "/books/{$book->id}", 'success' => true],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        Storage::disk('public')->delete($book->image);
        $book->delete();
        return ['success' => true];
    }
}
