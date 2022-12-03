<?php
namespace LaravelOauth2Client\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use LaravelOauth2Client\Models\Oauth2AccessToken;
use LaravelOauth2Client\Oauth2Service;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * @property Oauth2AccessToken[]|Collection $oauth2Tokens
 */
trait HasOauth2Tokens
{
    public function oauth2Tokens(): MorphMany
    {
        return $this->morphMany(Oauth2AccessToken::class, 'tokenable');
    }

    /**
     * @throws IdentityProviderException
     * @throws ModelNotFoundException
     */
    public function getFreshAccessToken(AbstractProvider $provider, ?string $providerName = null): Oauth2AccessToken
    {
        /** @var ?Oauth2AccessToken $token */
        $token = $this->oauth2Tokens
            ->where('provider', $providerName ?? Oauth2Service::guessProviderName($provider))
            ->firstOrFail();

        if ($token->hasExpired() && $token->getRefreshToken() !== null) {
            /** @var Oauth2Service $service */
            $service = app(Oauth2Service::class, [
                'provider' => $provider,
                'providerName' => $providerName,
            ]);

            $freshToken = $service->exchangeRefreshToken($token);
            $freshToken->save();

            $token->delete();

            return $freshToken;
        }

        return $token;
    }
}
