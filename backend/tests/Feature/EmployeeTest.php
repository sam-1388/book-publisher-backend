<?php

use Illuminate\Http\UploadedFile;

test('Stores correctly', function () {
    
    $response = $this->post('/employees',[
        'name'=>fake()->name(),
        'age'=>fake()->numberBetween(20,80),
        'rating'=>3,
        'notes'=>fake()->text(),
        'image'=>UploadedFile::fake()->image('pfp.png',200,400),
    ]);

    $response->assertStatus(200);
});
