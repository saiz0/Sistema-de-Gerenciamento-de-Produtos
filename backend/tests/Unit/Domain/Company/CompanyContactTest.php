<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Company;

use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CompanyContactTest extends TestCase
{
    #[DataProvider('invalidEmails')]
    public function test_rejects_invalid_emails(string $email): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailAddress($email);
    }

    #[DataProvider('invalidPhones')]
    public function test_rejects_phones_without_valid_length_and_ddd(string $phone): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PhoneNumber($phone);
    }

    public static function invalidEmails(): array
    {
        return [[''], ['email-invalido'], [str_repeat('a', 250).'@mail.com']];
    }

    public static function invalidPhones(): array
    {
        return [[''], ['99999999'], ['00999999999'], ['10999999999'], ['719999999999']];
    }
}
