<?php

use App\Models\User;

it('can register a user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('can login a user', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user']);
});

it('can logout a user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('api-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/logout');

    $response->assertOk()->assertJson(['message' => 'Logged out']);
});
