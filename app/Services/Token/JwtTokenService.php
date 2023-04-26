<?php


namespace App\Services\Token;


use DateTimeImmutable;
use Lcobucci\JWT\Token;

class JwtTokenService implements Tokenable
{

    public function issuedBy(string $issuer): Tokenable
    {
        // TODO: Implement issuedBy() method.
    }

    public function claimWith(string $name, mixed $value): Tokenable
    {
        // TODO: Implement claimWith() method.
    }

    public function expiresAt(DateTimeImmutable $expiration): Tokenable
    {
        // TODO: Implement expiresAt() method.
    }

    public function token(): Token
    {
        // TODO: Implement token() method.
    }

    public function parse(string $token): Token
    {
        // TODO: Implement parse() method.
    }

    public function validate(string $token): bool
    {
        // TODO: Implement validate() method.
    }
}
