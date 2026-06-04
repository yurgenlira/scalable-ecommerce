<?php

use App\Modules\Identity\Domain\Models\User;

it('registers a user and returns a token', function () {
    $this->postJson('/api/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'secret12',
    ])->assertCreated()->assertJsonStructure(['token']);

    expect(User::where('email', 'ana@example.com')->exists())->toBeTrue();
});

it('logs in with valid credentials', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'secret12',
    ]);

    $this->postJson('/api/login', [
        'email' => 'ana@example.com',
        'password' => 'secret12',
    ])->assertOk()->assertJsonStructure(['token']);
});
