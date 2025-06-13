<?php

use App\Models\Translation;
use App\Models\User;
use App\Models\Locale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

uses(Tests\TestCase::class);

it('will create Locale Data', function () {
    $arr = [
        [
            'name' => 'English',
            'short_code' => 'en',
            'created_at' => now(),
            'updated_at' => now()
        ], [
            'name' => 'French',
            'short_code' => 'fr',
            'created_at' => now(),
            'updated_at' => now()
        ], [
            'name' => 'Espanol',
            'short_code' => 'es',
            'created_at' => now(),
            'updated_at' => now()
        ],
    ];

    Locale::query()->truncate();
    Locale::query()->insert($arr);

    expect(Locale::count())->toBe(3);
});

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
    $this->localeCreated = Locale::query()->first();
});

it('can list translations', function () {
    Translation::factory()->create([
        'key' => 'greeting.hello',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Hello',
        'tags' => ['homepage'],
    ]);

    $response = getJson('/api/translations');

    $response->assertOk()
        ->assertJsonStructure([
            'data', 'total', 'current_page', 'per_page', 'last_page',
        ]);
});

it('can create a translation', function () {
    $response = postJson('/api/translations', [
        'key' => 'greeting2.hello',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Hello',
        'tags' => ['homepage'],
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Translation Created.',
        ]);

    expect(Translation::where('key', 'greeting2.hello')->exists())->toBeTrue(); // ✅ Corrected key
});

it('can show a translation', function () {
    $translation = Translation::factory()->create([
        'key' => 'greeting3.hi',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Hi',
        'tags' => ['footer'],
    ]);

    $response = getJson("/api/translations/{$translation->id}");

    $response->assertOk()
        ->assertJsonFragment([
            'id' => $translation->id,
            'key' => 'greeting3.hi', // ✅ Fixed key
        ]);
});

it('can update a translation', function () {
    $translation = Translation::factory()->create([
        'key' => 'greeting.bye',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Bye',
        'tags' => ['footer'],
    ]);

    $response = putJson("/api/translations/{$translation->id}", [
        'key' => 'greeting.goodbye',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Goodbye',
        'tags' => ['footer', 'updated'],
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Translation Updated.',
        ]);

    expect(Translation::find($translation->id)->key)->toBe('greeting.goodbye');
});

it('can delete a translation', function () {
    $translation = Translation::factory()->create([
        'locale_id' => $this->localeCreated->id,
    ]);

    $response = deleteJson("/api/translations/{$translation->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Translation deleted.',
        ]);

    expect(Translation::find($translation->id))->toBeNull();
});

it('can export translations as JSON', function () {
    Translation::factory()->create([
        'key' => 'greeting5.hello',
        'locale_id' => $this->localeCreated->id,
        'content' => 'Hello',
        'tags' => ['homepage'],
    ]);

    $response = getJson('/api/translations/export/json');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/json');
    $response->assertHeader('Content-Disposition', 'attachment; filename="translations.json"');
});
