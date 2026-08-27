<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers\Api\V1;

use Application\Product\DTOs\CreateProductData;
use Application\Product\DTOs\SearchProductsData;
use Application\Product\DTOs\UpdateProductData;
use Application\Product\UseCases\ActivateProduct;
use Application\Product\UseCases\CreateProduct;
use Application\Product\UseCases\DeactivateProduct;
use Application\Product\UseCases\DeleteProduct;
use Application\Product\UseCases\ForceDeleteProduct;
use Application\Product\UseCases\GetProduct;
use Application\Product\UseCases\ListProducts;
use Application\Product\UseCases\RestoreProduct;
use Application\Product\UseCases\UpdateProduct;
use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;
use Illuminate\Http\JsonResponse;
use Presentation\Http\Requests\Product\ForceDeleteProductRequest;
use Presentation\Http\Requests\Product\ListProductsRequest;
use Presentation\Http\Requests\Product\StoreProductRequest;
use Presentation\Http\Requests\Product\UpdateProductRequest;
use Presentation\Http\Resources\ProductResource;
use Presentation\Http\Responses\ApiMessages;
use Presentation\Http\Responses\ApiResponse;

final class ProductController
{
    public function index(ListProductsRequest $request, ListProducts $useCase): JsonResponse
    {
        $data = $request->validated();
        $page = $useCase->execute(new SearchProductsData(
            name: $data['name'] ?? null,
            status: isset($data['status']) ? ProductStatus::from($data['status']) : null,
            companyId: isset($data['company_id']) ? (int) $data['company_id'] : null,
            deleted: isset($data['deleted']) ? DeletedFilter::from($data['deleted']) : DeletedFilter::Without,
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15),
        ));

        return ApiResponse::success(
            data: array_map(ProductResource::make(...), $page->items),
            meta: [
                'current_page' => $page->currentPage,
                'per_page' => $page->perPage,
                'total' => $page->total,
                'last_page' => $page->lastPage,
            ],
        );
    }

    public function store(StoreProductRequest $request, CreateProduct $useCase): JsonResponse
    {
        $data = $request->validated();
        $product = $useCase->execute(new CreateProductData(
            companyId: (int) $data['company_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (string) $data['price'],
            internalCode: $data['internal_code'],
            status: ProductStatus::from($data['status'] ?? ProductStatus::Active->value),
        ));

        return ApiResponse::success(ProductResource::make($product), ApiMessages::CREATED, 201);
    }

    public function show(int $product, GetProduct $useCase): JsonResponse
    {
        return ApiResponse::success(ProductResource::make($useCase->execute($product)));
    }

    public function update(int $product, UpdateProductRequest $request, UpdateProduct $useCase): JsonResponse
    {
        $data = $request->validated();
        $updated = $useCase->execute($product, new UpdateProductData(
            companyId: (int) $data['company_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (string) $data['price'],
            internalCode: $data['internal_code'],
        ));

        return ApiResponse::success(ProductResource::make($updated), ApiMessages::UPDATED);
    }

    public function activate(int $product, ActivateProduct $useCase): JsonResponse
    {
        return ApiResponse::success(
            ProductResource::make($useCase->execute($product)),
            ApiMessages::STATUS_UPDATED,
        );
    }

    public function deactivate(int $product, DeactivateProduct $useCase): JsonResponse
    {
        return ApiResponse::success(
            ProductResource::make($useCase->execute($product)),
            ApiMessages::STATUS_UPDATED,
        );
    }

    public function destroy(int $product, DeleteProduct $useCase): JsonResponse
    {
        $useCase->execute($product);

        return ApiResponse::success(message: ApiMessages::DELETED);
    }

    public function restore(int $product, RestoreProduct $useCase): JsonResponse
    {
        return ApiResponse::success(
            ProductResource::make($useCase->execute($product)),
            ApiMessages::RESTORED,
        );
    }

    public function forceDestroy(
        int $product,
        ForceDeleteProductRequest $request,
        ForceDeleteProduct $useCase,
    ): JsonResponse {
        $useCase->execute($product, (bool) $request->validated('confirmed'));

        return ApiResponse::success(message: ApiMessages::DELETED);
    }
}
