<?php

namespace Dystcz\GetcandyApi\Domain\Collections\Http\Controllers;

use Dystcz\GetcandyApi\Domain\Api\Http\Api\Responses\ErrorNotFoundResponse;
use Dystcz\GetcandyApi\Domain\Collections\Http\Api\Responses\CollectionProductsShowResponse;
use Dystcz\GetcandyApi\Domain\Collections\Http\Resources\CollectionResource;
use GetCandy\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CollectionProductsController extends Controller
{
    /**
     * Show Collection with Products.
     *
     * Show collection and lists its products.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResource
     * @throws \Illuminate\Database\Exceptions\ModelNotFoundException
     */
    #[OpenApi\Operation(tags: ['collections'])]
    #[OpenApi\Response(factory: CollectionProductsShowResponse::class, statusCode: 200)]
    #[OpenApi\Response(factory: ErrorNotFoundResponse::class, statusCode: 404)]
    public function show(Request $request, string $slug): JsonResource
    {
        $collection = Collection::query()
            ->whereHas('defaultUrl', fn ($query) => $query->where('slug', $slug))
            ->with([
                'thumbnail',
                'products.variants.basePrices',
                'products.defaultUrl',
            ])
            ->firstOrFail();

        return new CollectionResource($collection);
    }
}
