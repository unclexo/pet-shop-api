<?php

namespace Tests\Unit\Services\Token;


use App\Services\Token\JwtTokenService;
use Carbon\CarbonImmutable;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Tests\TestCase;

class JwtTokenServiceTest extends TestCase
{
    public function test_jwt_token_service_exists_in_service_container()
    {
        $this->assertInstanceOf(JwtTokenService::class, app('jwt'));
    }

    public function test_it_can_create_jwt_token()
    {
        $this->assertInstanceOf(Token::class, $token = app('jwt')->token());
        $this->assertTrue(str_starts_with($token->toString(), 'eyJ'));
    }

    public function test_it_can_parse_a_given_token()
    {
        $this->assertInstanceOf(
            Token::class,
            app('jwt')->parse(app('jwt')->token()->toString())
        );
    }

    public function test_it_can_raise_exception_for_a_invalid_token()
    {
        $this->withoutExceptionHandling();

        $this->expectException(InvalidTokenStructure::class);

        app('jwt')->parse('');
        app('jwt')->parse('invalid token given');
        app('jwt')->parse('invalid.token given');

        $this->assertNotInstanceOf(
            Token::class,
            app('jwt')->parse('a invalid token')
        );
    }

    public function test_it_can_validate_a_given_token()
    {
        $this->assertTrue(app('jwt')->validate(
            app('jwt')->token()->toString()
        ));
    }

    public function test_it_can_construct_a_token_with_given_criteria()
    {
        $issuedBy = 'test_issue';
        $withClaim = ['uid' => 123];
        $expiresAt = CarbonImmutable::now()->addHours(3);

        $token = (new JwtTokenService(
            $issuedBy,
            $withClaim,
            $expiresAt,
        ))->token();

        $this->assertInstanceOf(Token::class, $token);

        $this->assertTrue($token->hasBeenIssuedBy($issuedBy));

        $this->assertSame(123, $token->claims()->get('uid'));

        $this->assertFalse($token->isExpired(now()->addMinutes(179)));
    }

    public function test_it_can_construct_a_jwt_token_via_methods_chain()
    {
        $issuedBy = 'test_issue';
        $expiresAt = CarbonImmutable::now()->addHours(3);

        $token = app('jwt')
            ->issuedBy($issuedBy)
            ->claimWith('uid', 123)
            ->expiresAt($expiresAt)
            ->token();

        $this->assertInstanceOf(Token::class, $token);

        $this->assertTrue($token->hasBeenIssuedBy($issuedBy));

        $this->assertSame(123, $token->claims()->get('uid'));

        $this->assertFalse($token->isExpired(now()->addMinutes(179)));
    }

    public function test_it_users_asymmetric_algorithm_by_default()
    {
        $issuedBy = 'test_issuer';

        $jwt = app('jwt')
            ->issuedBy($issuedBy)
            ->configureValidationConstraints([
                new IssuedBy($issuedBy),
                new SignedWith(
                    new Sha256(),
                    JwtTokenService::publicSigningKey()
                ),
            ]);

        $this->assertTrue($jwt->validate(
            app('jwt')
                ->issuedBy($issuedBy)
                ->token()
                ->toString()
        ));

        $this->assertFalse($jwt->validate(
            app('jwt')->token()->toString()
        ));

        $this->assertTrue(app('jwt')->validate(
            app('jwt')->token()->toString()
        ));
    }
}
