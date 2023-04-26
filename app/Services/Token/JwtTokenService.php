<?php


namespace App\Services\Token;


use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtTokenService implements Tokenable
{
    private string $issuedBy;

    private array $withClaim = [];

    private DateTimeImmutable $expiresAt;

    private Signer $signer;

    private Key $privateSigningKey;

    private Key $verificationKey;

    private Configuration $configuration;

    private array $validationConstraints = [];

    private static Key $publicSigningKey;

    public static function publicSigningKey(): Key
    {
        return self::$publicSigningKey = InMemory::file(
            Storage::path(env('JWT_TOKEN_PUBLIC_KEY_PATH'))
        );
    }

    public function __construct(
        string $issuedBy = null,
        array $withClaim = [],
        DateTimeImmutable $expiresAt = null,
        array $validationConstraints = []
    ) {
        $this->issuedBy = $issuedBy ?? config('app.url');

        $this->withClaim = count(array_keys($withClaim)) > 0
            ? $withClaim
            : ['unique_id' => Str::random(21)];

        $this->expiresAt = $expiresAt ?? CarbonImmutable::now()->addHour(2);

        $this->signer = new Sha256();

        $this->privateSigningKey = InMemory::file(Storage::path(
            env('JWT_TOKEN_PRIVATE_KEY_PATH')
        ));

        $this->verificationKey = InMemory::base64Encoded(
            substr(config('app.key'), 7)
        );

        $this->configuration = Configuration::forAsymmetricSigner(
            $this->signer,
            $this->privateSigningKey,
            $this->verificationKey,
        );

        if (count($validationConstraints) > 0) {
            $this->validationConstraints = $validationConstraints;
        }
    }

    public function issuedBy(string $issuer): Tokenable
    {
        $this->issuedBy = $issuer;

        return $this;
    }

    public function claimWith(string $name, mixed $value): Tokenable
    {
        $this->withClaim[$name] = $value;

        return $this;
    }

    public function expiresAt(DateTimeImmutable $expiration): Tokenable
    {
        $this->expiresAt = $expiration;

        return $this;
    }

    public function configureValidationConstraints(array $constraints): Tokenable
    {
        $this->validationConstraints = $constraints;

        return $this;
    }

    public function token(): Token
    {
        $tokenBuilder = $this->configuration->builder()
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

        return $tokenBuilder->getToken(
            $this->configuration->signer(),
            $this->configuration->signingKey()
        );
    }

    public function parse(string $token): Token
    {
        $parser = new Parser(new JoseEncoder());

        return $parser->parse($token);
    }

    public function validate(string $token): bool
    {
        (count($this->validationConstraints) === 0)
            ? $this->validationConstraints =
            $this->setDefaultValidationConstraints()
            : $this->validationConstraints;

        $this->configuration->setValidationConstraints(
            ...$this->validationConstraints
        );

        return $this->configuration->validator()->validate(
            $this->parse($token),
            ...$this->configuration->validationConstraints()
        );
    }

    private function setDefaultValidationConstraints()
    {
        return [
            new SignedWith($this->signer, self::publicSigningKey())
        ];
    }
}
