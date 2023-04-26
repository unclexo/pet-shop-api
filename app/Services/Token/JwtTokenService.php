<?php


namespace App\Services\Token;


use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Validator;

class JwtTokenService implements Tokenable
{
    private string $issuedBy;

    private array $withClaim = [];

    private DateTimeImmutable $expiresAt;

    public function __construct(
        string $issuedBy = null,
        array $withClaim = [],
        DateTimeImmutable $expiresAt = null
    ) {
        $this->issuedBy = $issuedBy ?? config('app.url');

        $this->withClaim = count(array_keys($withClaim)) > 0
            ? $withClaim
            : ['unique_id' => Str::random(21)];

        $this->expiresAt = $expiresAt ?? CarbonImmutable::now()->addHour(2);
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

        $tokenBuilder = $tokenBuilder
            ->issuedBy($this->issuedBy)
            ->expiresAt($this->expiresAt);

        foreach ($this->withClaim as $key => $value) {
            if (! is_string($key) || ! $value) {
                throw new InvalidArgumentException(
                    "Claim(s) was not configured correctly."
                );
            }

            $tokenBuilder = $tokenBuilder->withClaim($key, $value);
        }

        return $tokenBuilder->getToken($algorithm, $signingKey);
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
