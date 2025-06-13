<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranslation;
use App\Http\Requests\UpdateTranslation;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationController extends Controller
{
    private $success = false, $message = '', $statusCode = 400;

    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List all translations",
     *     tags={"Translations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filter translations by tag (e.g. greeting)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         description="Search by translation key",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         description="Search by translation content",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=30),
     *             @OA\Property(property="last_page", type="integer", example=4)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Translation::query();

        if ($request->has('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }
        if ($request->has('key')) {
            $query->where('key', 'like', "%{$request->key}%");
        }
        if ($request->has('content')) {
            $query->where('content', 'like', "%{$request->content}%");
        }

        $perPage = $request->get('per_page', 30);
        $translations = $query->paginate($perPage);

        return response()->json([
            'data' => $translations->items(),
            'total' => $translations->total(),
            'current_page' => $translations->currentPage(),
            'per_page' => $translations->perPage(),
            'last_page' => $translations->lastPage(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create translation entry",
     *     tags={"Translations"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key","locale","content"},
     *             @OA\Property(property="key", type="string", example="greeting.hello"),
     *             @OA\Property(property="locale_id", type="integer", example="1"),
     *             @OA\Property(property="content", type="string", example="Hello"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation created or updated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="Key"),
     *                     @OA\Property(property="message", type="string", example="The Key is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreTranslation $request)
    {
        $data = $request->all();
        try {
            Translation::query()->create($data);

            $this->message = 'Translation Created.';
            $this->statusCode = 200;
            $this->success = true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json([
            'success' => $this->success,
            'message' => $this->message
        ], $this->statusCode);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get a specific locale by ID",
     *     tags={"Translations"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation details",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function show(Translation $translation)
    {
        return response()->json($translation);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update an existing translation",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key","locale","content"},
     *             @OA\Property(property="key", type="string", example="greeting.hello"),
     *             @OA\Property(property="locale_id", type="integer", example="1"),
     *             @OA\Property(property="content", type="string", example="Hello"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateTranslation $request, $id)
    {
        $data = $request->all();
        try {
            $translation = Translation::query()->findOrFail($id);

            $translation->update($data);

            $this->message = 'Translation Updated.';
            $this->statusCode = 200;
            $this->success = true;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json([
            'success' => $this->success,
            'message' => $this->message
        ], $this->statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a locale",
     *     tags={"Locales"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale deletion response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Translation deleted.")
     *         )
     *     )
     * )
     */
    public function destroy(Translation $translation)
    {
        try {
            $translation->delete();

            $this->message = 'Translation deleted.';
            $this->success = true;
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json(['success' => $this->success, 'message' => $this->message], $this->statusCode);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/export/json",
     *     summary="Export all translations as JSON",
     *     tags={"Translations"},
     *     @OA\Response(
     *         response=200,
     *         description="Translations exported in JSON format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="key", type="string", example="greeting.hello"),
     *                     @OA\Property(property="locale", type="string", example="en"),
     *                     @OA\Property(property="content", type="string", example="Hello"),
     *                     @OA\Property(property="tags", type="string", example="homepage")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function export()
    {
        $response = new StreamedResponse(function () {
            echo '[';
            $first = true;

            $records = DB::table('translations')
                ->join('locales as l', 'l.id', '=', 'translations.locale_id')
                ->select('translations.key', 'l.short_code as locale', 'translations.content', 'translations.tags')
                ->orderBy('translations.id')
                ->cursor(); // lazy-load rows one at a time

            foreach ($records as $translation) {
                if (!$first) {
                    echo ',';
                }
                echo json_encode($translation, JSON_UNESCAPED_UNICODE);
                $first = false;
            }

            echo ']';
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename="translations.json"');

        return $response;
    }
}
