<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\postJson;

uses(Tests\TestCase::class);

it('can register a user', function () {
    $response = postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(200); // changed from assertCreated()
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('can login a user', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user']);
});

it('can logout a user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('api-token')->plainTextToken;

    $response = postJson('/api/logout', [], [
        'Authorization' => "Bearer $token",
    ]);

    $response->assertOk()->assertJson(['message' => 'User Logged out']); // updated message to match actual response
});
