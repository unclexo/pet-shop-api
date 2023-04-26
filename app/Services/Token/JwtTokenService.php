<?php


namespace App\Services\Token;


use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

class JwtTokenService implements Tokenable
{
    private string $issuedBy;

    private array $withClaim = [];

    private DateTimeImmutable $expiresAt;

    public function __construct()
    {
        $this->issuedBy = 'http://example.com';
        $this->withClaim = ['uid', 1];
        $this->expiresAt = CarbonImmutable::now()->addHour(1);
    }

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
            ->issuedBy($this->issuedBy)
            ->expiresAt($this->expiresAt)
            ->withClaim($this->withClaim[0], $this->withClaim[1])
            ->getToken($algorithm, $signingKey);
    }

    public function parse(string $token): Token
    {
        $parser = new Parser(new JoseEncoder());

        return $parser->parse($token);
    }

    public function validate(string $token): bool
    {
        $parser = new Parser(new JoseEncoder());

        $token = $parser->parse($token);

        $validator = new Validator();

        if ($token->isExpired(now())) {
            return false;
        }

        if (! $validator->validate($token, new IssuedBy($this->issuedBy))) {
            return false;
        }

        return true;
    }
}
