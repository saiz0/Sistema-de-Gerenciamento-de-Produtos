<?php

declare(strict_types=1);

namespace Domain\Company\Entities;

use DateTimeImmutable;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use InvalidArgumentException;

final class Company
{
    public function __construct(
        private ?int $id,
        private string $name,
        private Cnpj $cnpj,
        private EmailAddress $email,
        private PhoneNumber $phone,
        private CompanyStatus $status,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
        private ?DateTimeImmutable $deletedAt = null,
    ) {
        $this->name = self::validatedName($name);
    }

    public static function create(
        string $name,
        Cnpj $cnpj,
        EmailAddress $email,
        PhoneNumber $phone,
        CompanyStatus $status = CompanyStatus::Active,
    ): self {
        return new self(null, $name, $cnpj, $email, $phone, $status);
    }

    public function update(string $name, Cnpj $cnpj, EmailAddress $email, PhoneNumber $phone): void
    {
        $this->name = self::validatedName($name);
        $this->cnpj = $cnpj;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function activate(): void
    {
        $this->status = CompanyStatus::Active;
    }

    public function deactivate(): void
    {
        $this->status = CompanyStatus::Inactive;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function cnpj(): Cnpj
    {
        return $this->cnpj;
    }

    public function email(): EmailAddress
    {
        return $this->email;
    }

    public function phone(): PhoneNumber
    {
        return $this->phone;
    }

    public function status(): CompanyStatus
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

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
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
}
