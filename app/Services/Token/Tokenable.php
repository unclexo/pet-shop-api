<?php

declare(strict_types=1);


namespace App\Services\Token;


use DateTimeImmutable;
use Lcobucci\JWT\Token;

interface Tokenable
{
    public function issuedBy(string $issuer): Tokenable;

    public function claimWith(string $name, mixed $value): Tokenable;

    public function expiresAt(DateTimeImmutable $expiration): Tokenable;

    public function token(): Token;

    public function parse(string $token): Token;

    public function validate(string $token): bool;
}
