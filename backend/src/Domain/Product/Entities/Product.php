<?php

declare(strict_types=1);

namespace Domain\Product\Entities;

use DateTimeImmutable;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;
use InvalidArgumentException;

final class Product
{
    public function __construct(
        private ?int $id,
        private int $companyId,
        private string $name,
        private ?string $description,
        private Price $price,
        private InternalCode $internalCode,
        private ProductStatus $status,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
        private ?DateTimeImmutable $deletedAt = null,
        private ?DateTimeImmutable $deletedByCompanyAt = null,
    ) {
        $this->companyId = self::validatedCompanyId($companyId);
        $this->name = self::validatedName($name);
        $this->description = self::validatedDescription($description);
    }

    public static function create(
        int $companyId,
        string $name,
        ?string $description,
        Price $price,
        InternalCode $internalCode,
        ProductStatus $status = ProductStatus::Active,
    ): self {
        return new self(null, $companyId, $name, $description, $price, $internalCode, $status);
    }

    public function update(
        int $companyId,
        string $name,
        ?string $description,
        Price $price,
        InternalCode $internalCode,
    ): void {
        $this->companyId = self::validatedCompanyId($companyId);
        $this->name = self::validatedName($name);
        $this->description = self::validatedDescription($description);
        $this->price = $price;
        $this->internalCode = $internalCode;
    }

    public function activate(): void
    {
        $this->status = ProductStatus::Active;
    }

    public function deactivate(): void
    {
        $this->status = ProductStatus::Inactive;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function companyId(): int
    {
        return $this->companyId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function price(): Price
    {
        return $this->price;
    }

    public function internalCode(): InternalCode
    {
        return $this->internalCode;
    }

    public function status(): ProductStatus
    {
        return $this->status;
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function deletedByCompanyAt(): ?DateTimeImmutable
    {
        return $this->deletedByCompanyAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function wasDeletedByCompany(): bool
    {
        return $this->deletedByCompanyAt !== null;
    }

    private static function validatedCompanyId(int $companyId): int
    {
        if ($companyId <= 0) {
            throw new InvalidArgumentException('O produto deve estar vinculado a uma empresa válida.');
        }

        return $companyId;
    }

    private static function validatedName(string $name): string
    {
        $name = trim($name);
        $length = mb_strlen($name);

        if ($length < 3 || $length > 150) {
            throw new InvalidArgumentException('O nome deve conter entre 3 e 150 caracteres.');
        }

        return $name;
    }

    private static function validatedDescription(?string $description): ?string
    {
        if ($description === null || trim($description) === '') {
            return null;
        }

        $description = trim($description);

        if (mb_strlen($description) > 2000) {
            throw new InvalidArgumentException('A descrição deve conter no máximo 2.000 caracteres.');
        }

        return $description;
    }
}
