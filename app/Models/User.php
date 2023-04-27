<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Requests\Auth\UserListingRequest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Token;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'is_admin',
        'email',
        'password',
        'avatar',
        'address',
        'phone_number',
        'is_marketing',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'is_admin',
        'password',
        'remember_token',
        'last_login_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function password(): Attribute
    {
        return new Attribute(
            set: fn (string $password) => bcrypt($password)
        );
    }

    public function tokenize(string $title): void
    {
        $token = $this->createToken();
        $claims = $token->claims();

        try {
            DB::beginTransaction();

            JwtToken::where('user_uuid', $this->uuid)->delete();

            $this->jwtToken()->create([
                'unique_id' => $claims->get('unique_id'),
                'token_title' => $title,
                'expires_at' => $claims->get('exp'),
                'last_used_at' => now(),
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            abort(500);
        }

        $this->attributes['token'] = $token->toString();
    }

    public function createToken(): Token
    {
        return app('jwt')
            ->issuedBy(config('app.url'))
            ->claimWith('user_uuid', $this->uuid)
            ->expiresAt(CarbonImmutable::now()->addHours(
                env('JWT_TOKEN_EXPIRATION_HOURS', 14)
            ))
            ->token();
    }

    public function jwtToken(): HasOne
    {
        return $this->hasOne(JwtToken::class, 'user_uuid', 'uuid');
    }

    public function scopeNonAdminUsers(Builder $query): void
    {
        $query->where('is_admin', '!=', 1);
    }
}
