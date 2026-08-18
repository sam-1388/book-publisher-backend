<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status') ?? null;
        $search = $request->query('query') ?? null;
        $temp = $this->getCorrectType($status);
       

        $books = Auth::user()->books()
            ->when($temp, function (Builder $query, $temp) {
                $query->where('status', $temp);
            })
            ->when($search, function (Builder $query, $search) {
                $query->where('title','LIKE', "%{$search}%");
            })
            ->paginate(14)
            ->withQueryString();


        foreach ($books as $book) {
            $book->image = url($book->image);
        }
        return $books;
    }
    public function search(Request $request) {}

    public function getBooksForGuests(Request $request)
    {
        try {
            $data = $request->validate(['user_id' => ['required', Rule::exists('users', 'id')]]);
            $books = User::find($data['user_id'])
                ->books()
                ->select(['id', 'title', 'edition', 'author', 'number_of_copies'])
                ->get();

            return $books;
        } catch (ValidationException $th) {
            return response($th->errors(), 422);
        }
    }



    private function getCorrectType(?string $x)
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
                'number_of_copies' => ['between:0,100000', 'required'],
                'image' => ['image', 'required', 'max:5120'],
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


            $book = $request->user()->books()->create([
                ...$validator->safe()->except(['image']),
                'image' => $path
            ]);

            return response(
                ['redirect' => "/books/$book->id", 'success' => true],
                200
            );
        }
    }


    public function updateStatus(Request $request, Book $book)
    {

        try {

            $request->validate([
                'bookStatus' => ['required', Rule::in(['need translation', 'need copyEditing', 'need typeSetting', 'need proofReading', 'ready for printing'])]
            ]);
        } catch (ValidationException $th) {
            return response($th->errors(), 422);
        }

        $book->update([
            'status' => $request->input('bookStatus')
        ]);
        $book->save();
        return ['success' => true];
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
                'image' => ['image', 'required'],
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
        $book->tasks()->delete();
        $book->delete();
        return ['success' => true];
    }
}
