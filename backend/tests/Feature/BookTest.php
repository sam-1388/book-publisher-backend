<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\assertDatabaseCount;


pest()->use(RefreshDatabase::class);

it('views books successfully', function () {
    $book = Book::factory()->create([]);
    $user = User::factory()->create();

    $this->assertModelExists($book);
    $response = $this->actingAs($user, 'web')->get('/books');

    $response->assertJson(function (AssertableJson $json) use ($book) {
        $json->has('books', 1)
            ->has('books.0', function (AssertableJson $json) use ($book) {
                $json->where('author', $book->author)
                    ->where('id', $book->id)
                    ->where('title', $book->title)
                    ->where('page_count', $book->page_count)
                    ->where('publishing_year', $book->publishing_year)
                    ->where('edition', $book->edition)
                    ->where('number_of_copies', $book->number_of_copies)
                    ->where('image', $book->image) // to be put in <img src="....."/>
                    ->where('notes', $book->notes)
                    ->etc();
            });
    });
    $response->ddBody();
    $response->assertStatus(200);
});

it('stores a book successfully', function () {
    $user = User::factory()->create();
    Storage::fake('public');
    $book = [
        'title' => 'mortal kombat',
        'author' => 'soha X9D',
        'page_count' => 122,
        'publishing_year' => 2023,
        'edition' => '1st',
        'number_of_copies' => 234,
        'image' => UploadedFile::fake()->image('bookimg.png',200,100),
        'notes' => 'dumb notes here',
    ];
    $this->assertModelExists($user);
    $response = $this->actingAs($user, 'web')->postJson( '/books', $book);


    
    $response->assertStatus(200);
    
    assertDatabaseCount('books', 1);
    // dump(Storage::disk('public')->allFiles());
    // dump($book['image']->hashName());
    // dump(Storage::disk('public')->allFiles('images'));
    Storage::disk('public')->assertCount('images',1);
     Storage::disk('public')->assertExists("images/".$book['image']->hashName());
    // $response2 = $this->get($response);
    // dd($response2);
});
