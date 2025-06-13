<?php

namespace App\Http\Controllers\Api;

use App\Models\Locale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocaleRequest;
use App\Http\Requests\UpdateLocaleRequest;

class LocaleController extends Controller
{
    private $success = false, $message = '', $statusCode = 400;

    /**
     * @OA\Get(
     *     path="/api/locales",
     *     summary="Get list of locales",
     *     tags={"Locales"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Filter by name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="short_code",
     *         in="query",
     *         required=false,
     *         description="Filter by short code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of locales",
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
        $query = Locale::query();

        if ($request->has('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }
        if ($request->has('short_code')) {
            $query->where('short_code', 'like', "%{$request->short_code}%");
        }

        $perPage = $request->get('per_page', 30);
        $locales = $query->paginate($perPage);

        return response()->json([
            'data' => $locales->items(),
            'total' => $locales->total(),
            'current_page' => $locales->currentPage(),
            'per_page' => $locales->perPage(),
            'last_page' => $locales->lastPage(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/locales",
     *     summary="Create a new locale",
     *     tags={"Locales"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "short_code"},
     *             @OA\Property(property="name", type="string", example="English"),
     *             @OA\Property(property="short_code", type="string", example="en")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale created response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Locale created.")
     *         )
     *     )
     * )
     */
    public function store(StoreLocaleRequest $request)
    {
        try {
            Locale::query()->create($request->validated());

            $this->message = 'Locale created.';
            $this->success = true;
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json(['success' => $this->success, 'message' => $this->message], $this->statusCode);
    }

    /**
     * @OA\Get(
     *     path="/api/locales/{id}",
     *     summary="Get a specific locale by ID",
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
     *         description="Locale details",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function show(Locale $locale)
    {
        return response()->json($locale);
    }

    /**
     * @OA\Put(
     *     path="/api/locales/{id}",
     *     summary="Update a locale",
     *     tags={"Locales"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "short_code"},
     *             @OA\Property(property="name", type="string", example="English US"),
     *             @OA\Property(property="short_code", type="string", example="en-us")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale updated response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Locale updated.")
     *         )
     *     )
     * )
     */
    public function update(UpdateLocaleRequest $request, Locale $locale)
    {
        try {
            $locale->update($request->validated());

            $this->message = 'Locale updated.';
            $this->success = true;
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json(['success' => $this->success, 'message' => $this->message], $this->statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/api/locales/{id}",
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
     *             @OA\Property(property="message", type="string", example="Locale deleted.")
     *         )
     *     )
     * )
     */
    public function destroy(Locale $locale)
    {
        try {
            $locale->delete();

            $this->message = 'Locale deleted.';
            $this->success = true;
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json(['success' => $this->success, 'message' => $this->message], $this->statusCode);
    }
}
