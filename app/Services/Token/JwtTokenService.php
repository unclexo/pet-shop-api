<?php


namespace App\Services\Token;


use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;

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
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText(random_bytes(32));

        $now   = new DateTimeImmutable();

        return $tokenBuilder
            ->issuedBy('http://example.com')
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', 1)
            ->getToken($algorithm, $signingKey);
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
