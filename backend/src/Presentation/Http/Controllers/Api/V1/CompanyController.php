<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers\Api\V1;

use Application\Company\DTOs\CreateCompanyData;
use Application\Company\DTOs\SearchCompaniesData;
use Application\Company\DTOs\UpdateCompanyData;
use Application\Company\UseCases\ActivateCompany;
use Application\Company\UseCases\CreateCompany;
use Application\Company\UseCases\DeactivateCompany;
use Application\Company\UseCases\DeleteCompany;
use Application\Company\UseCases\ForceDeleteCompany;
use Application\Company\UseCases\GetCompany;
use Application\Company\UseCases\ListCompanies;
use Application\Company\UseCases\RestoreCompany;
use Application\Company\UseCases\UpdateCompany;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;
use Illuminate\Http\JsonResponse;
use Presentation\Http\Requests\Company\ForceDeleteCompanyRequest;
use Presentation\Http\Requests\Company\ListCompaniesRequest;
use Presentation\Http\Requests\Company\StoreCompanyRequest;
use Presentation\Http\Requests\Company\UpdateCompanyRequest;
use Presentation\Http\Resources\CompanyResource;
use Presentation\Http\Responses\ApiMessages;
use Presentation\Http\Responses\ApiResponse;

final class CompanyController
{
    public function index(ListCompaniesRequest $request, ListCompanies $useCase): JsonResponse
    {
        $data = $request->validated();
        $page = $useCase->execute(new SearchCompaniesData(
            name: $data['name'] ?? null,
            status: isset($data['status']) ? CompanyStatus::from($data['status']) : null,
            deleted: isset($data['deleted']) ? DeletedFilter::from($data['deleted']) : DeletedFilter::Without,
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15),
        ));

        return ApiResponse::success(
            data: array_map(CompanyResource::make(...), $page->items),
            meta: [
                'current_page' => $page->currentPage,
                'per_page' => $page->perPage,
                'total' => $page->total,
                'last_page' => $page->lastPage,
            ],
        );
    }

    public function store(StoreCompanyRequest $request, CreateCompany $useCase): JsonResponse
    {
        $data = $request->validated();
        $company = $useCase->execute(new CreateCompanyData(
            name: $data['name'],
            cnpj: $data['cnpj'],
            email: $data['email'],
            phone: $data['phone'],
            status: CompanyStatus::from($data['status'] ?? CompanyStatus::Active->value),
        ));

        return ApiResponse::success(CompanyResource::make($company), ApiMessages::CREATED, 201);
    }

    public function show(int $company, GetCompany $useCase): JsonResponse
    {
        return ApiResponse::success(CompanyResource::make($useCase->execute($company)));
    }

    public function update(int $company, UpdateCompanyRequest $request, UpdateCompany $useCase): JsonResponse
    {
        $data = $request->validated();
        $updated = $useCase->execute($company, new UpdateCompanyData(
            name: $data['name'],
            cnpj: $data['cnpj'],
            email: $data['email'],
            phone: $data['phone'],
        ));

        return ApiResponse::success(CompanyResource::make($updated), ApiMessages::UPDATED);
    }

    public function activate(int $company, ActivateCompany $useCase): JsonResponse
    {
        return ApiResponse::success(
            CompanyResource::make($useCase->execute($company)),
            ApiMessages::STATUS_UPDATED,
        );
    }

    public function deactivate(int $company, DeactivateCompany $useCase): JsonResponse
    {
        return ApiResponse::success(
            CompanyResource::make($useCase->execute($company)),
            ApiMessages::STATUS_UPDATED,
        );
    }

    public function destroy(int $company, DeleteCompany $useCase): JsonResponse
    {
        $useCase->execute($company);

        return ApiResponse::success(message: ApiMessages::DELETED);
    }

    public function restore(int $company, RestoreCompany $useCase): JsonResponse
    {
        return ApiResponse::success(
            CompanyResource::make($useCase->execute($company)),
            ApiMessages::RESTORED,
        );
    }

    public function forceDestroy(
        int $company,
        ForceDeleteCompanyRequest $request,
        ForceDeleteCompany $useCase,
    ): JsonResponse {
        $useCase->execute($company, (bool) $request->validated('confirmed'));

        return ApiResponse::success(message: ApiMessages::DELETED);
    }
}
