<?php
namespace LaravelOAuth2Client\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use LaravelOAuth2Client\Models\OAuth2AccessToken;
use LaravelOAuth2Client\OAuth2Service;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * @property OAuth2AccessToken[]|Collection $oauth2Tokens
 */
trait HasOAuth2Tokens
{
    public function oauth2Tokens(): MorphMany
    {
        return $this->morphMany(OAuth2AccessToken::class, 'tokenable');
    }

    /**
     * @throws IdentityProviderException
     * @throws ModelNotFoundException
     */
    public function getFreshAccessToken(AbstractProvider $provider, ?string $providerName = null): OAuth2AccessToken
    {
        /** @var ?OAuth2AccessToken $token */
        $token = $this->oauth2Tokens
            ->where('provider', $providerName ?? OAuth2Service::guessProviderName($provider))
            ->firstOrFail();

        if ($token->hasExpired() && $token->getRefreshToken() !== null) {
            /** @var OAuth2Service $service */
            $service = app(OAuth2Service::class, [
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
