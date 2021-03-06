<?php

namespace Dystcz\GetcandyApi\Domain\Collections\Http\Controllers;

use Dystcz\GetcandyApi\Domain\Api\Http\Api\Responses\ErrorNotFoundResponse;
use Dystcz\GetcandyApi\Domain\Collections\Http\Api\Responses\CollectionsIndexResponse;
use Dystcz\GetcandyApi\Domain\Collections\Http\Api\Responses\ShowCollectionResponse;
use Dystcz\GetcandyApi\Domain\Collections\Http\Resources\CollectionResource;
use GetCandy\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CollectionsController extends Controller
{
    /**
     * List collections.
     *
     * Lists all collections.
     *
     * @param Request $request
     * @return JsonResource
     */
    #[OpenApi\Operation(tags: ['collections'])]
    #[OpenApi\Response(factory: CollectionsIndexResponse::class, statusCode: 200)]
    public function index(Request $request): JsonResource
    {
        $collections = Collection::query()
            ->with([
                'thumbnail',
            ])
            ->get();

        return CollectionResource::collection($collections);
    }

    /**
     * Show collection.
     *
     * Show collection by slug.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResource
     * @throws \Illuminate\Database\Exceptions\ModelNotFoundException
     */
    #[OpenApi\Operation(tags: ['collections'])]
    #[OpenApi\Response(factory: ShowCollectionResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: ErrorNotFoundResponse::class, statusCode: 404)]
    public function show(Request $request, string $slug): JsonResource
    {
        $collection = Collection::query()
            ->whereHas('defaultUrl', fn ($query) => $query->where('slug', $slug))
            ->with([
                'thumbnail',
            ])
            ->firstOrFail();

        return new CollectionResource($collection);
    }
}
