<?php

use App\Models\Locale;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('can list locales', function () {
    Locale::factory()->count(3)->create();

    $response = getJson('/api/locales');

    $response->assertOk()
        ->assertJsonStructure([
            'data',
            'total',
            'current_page',
            'per_page',
            'last_page',
        ]);
});

it('can filter locales by name', function () {
    Locale::factory()->create(['name' => 'Urdu', 'short_code' => 'ur']);
    Locale::factory()->create(['name' => 'Punjabi', 'short_code' => 'pu']);
    Locale::factory()->create(['name' => 'Spanish', 'short_code' => 'sp']);

    $response = getJson('/api/locales?name=spanish');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data')[0]['name'])->toBe('Spanish');
});

it('can create a locale', function () {
    $payload = [
        'name' => 'French',
        'short_code' => 'fr',
    ];

    $response = postJson('/api/locales', $payload);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Locale created.',
        ]);

    expect(Locale::where($payload)->exists())->toBeTrue();
});

it('can show a locale', function () {
    $locale = Locale::factory()->create();

    $response = getJson("/api/locales/{$locale->id}");

    $response->assertOk()
        ->assertJson([
            'id' => $locale->id,
            'name' => $locale->name,
            'short_code' => $locale->short_code,
        ]);
});

it('can update a locale', function () {
    $locale = Locale::factory()->create([
        'name' => 'German',
        'short_code' => 'de',
    ]);

    $payload = [
        'name' => 'German Updated',
        'short_code' => 'de-updated',
    ];

    $response = putJson("/api/locales/{$locale->id}", $payload);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Locale updated.',
        ]);

    expect(Locale::find($locale->id)->name)->toBe('German Updated');
    expect(Locale::find($locale->id)->short_code)->toBe('de-updated');
});

it('can delete a locale', function () {
    $locale = Locale::factory()->create();

    $response = deleteJson("/api/locales/{$locale->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Locale deleted.',
        ]);

    expect(Locale::find($locale->id))->toBeNull();
});
