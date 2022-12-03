<?php

namespace LaravelOauth2Client;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;
use LaravelOauth2Client\Events\RefreshTokenExchanged;
use LaravelOauth2Client\Http\Requests\Oauth2CallbackRequest;
use LaravelOauth2Client\Models\Oauth2AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

class Oauth2Service
{
    protected AbstractProvider $provider;
    protected string $providerName;

    protected Store $session;
    protected Redirector $redirector;

    public function __construct(AbstractProvider $provider, ?string $providerName = null)
    {
        $this->provider = $provider;
        $this->providerName = $providerName ?? self::guessProviderName($provider);

        $this->session = app('session');
        $this->redirector = app('redirect');
    }

    public function init(array $scope): RedirectResponse
    {
        $url = $this->provider->getAuthorizationUrl([
            'scope' => $scope,
        ]);

        $this->saveState($this->provider->getState());

        if ($this->pkceEnabled()) {
            $this->saveVerifier($this->getPkceVerifier());
        }

        return $this->redirector->away($url);
    }

    /**
     * @throws IdentityProviderException
     */
    public function callback(Oauth2CallbackRequest $request): Oauth2AccessToken
    {
        if ($request->has('denied')) {
            throw new IdentityProviderException('OAuth2 callback: denied');
        }

        if (! $this->hasSavedState()) {
            throw new IdentityProviderException('OAuth2 callback: state is missing');
        }

        if ($this->pkceEnabled() && ! $this->hasSavedVerifier()) {
            throw new IdentityProviderException('OAuth2 callback: verifier is missing');
        }

        if ($request->get('state') !== $this->getSavedState()) {
            throw new IdentityProviderException('OAuth2 callback: state mismatch');
        }

        $options = [
            'code' => $request->get('code'),
        ];

        if ($this->pkceEnabled()) {
            $options['code_verifier'] = $this->getSavedVerifier();
        }

        $accessToken = $this->provider->getAccessToken('authorization_code', $options);

        $this->forgetStateAndVerifier();

        return $this->getModelForToken($accessToken);
    }

    /**
     * @throws IdentityProviderException
     */
    public function exchangeRefreshToken(Oauth2AccessToken $refreshableToken): Oauth2AccessToken
    {
        $accessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $refreshableToken->getRefreshToken(),
        ]);

        $model = $this->getModelForToken($accessToken);

        $model->tokenable_type = $refreshableToken->tokenable_type;
        $model->tokenable_id = $refreshableToken->tokenable_id;

        if (empty($model->getRefreshToken())) {
            $model->refresh_token = $refreshableToken->getRefreshToken();
        }

        RefreshTokenExchanged::dispatch($refreshableToken, $model);

        return $model;
    }

    public static function guessProviderName(AbstractProvider $provider): string
    {
        $class = get_class($provider);

        $class = substr($class, strrpos($class, '\\') + 1);
        $class = substr($class, 0, -strlen('Provider'));

        return strtolower($class);
    }

    protected function getModelForToken(AccessTokenInterface $accessToken): AccessTokenInterface
    {
        $model = Oauth2AccessToken::fillFromAccessToken($accessToken);
        $model->provider = $this->providerName;

        return $model;
    }

    protected function saveState(string $state): void
    {
        $this->session->put($this->getStateSessionKey(), $state);
    }

    protected function hasSavedState(): bool
    {
        return $this->session->has($this->getStateSessionKey());
    }

    protected function getSavedState(): ?string
    {
        return $this->session->get($this->getStateSessionKey());
    }

    protected function pkceEnabled(): bool
    {
        return method_exists($this->provider, 'getPkceVerifier');
    }

    protected function getPkceVerifier(): ?string
    {
        return $this->provider->getPkceVerifier();
    }

    protected function saveVerifier(string $verifier)
    {
        $this->session->put($this->getVerifierSessionKey(), $verifier);
    }

    protected function hasSavedVerifier(): bool
    {
        return $this->session->has($this->getVerifierSessionKey());
    }

    protected function getSavedVerifier(): ?string
    {
        return $this->session->get($this->getVerifierSessionKey());
    }

    protected function forgetStateAndVerifier()
    {
        $this->session->forget($this->getStateSessionKey());
        $this->session->forget($this->getVerifierSessionKey());
    }

    protected function getStateSessionKey(): string
    {
        return $this->providerName . '.oauth2.state';
    }

    protected function getVerifierSessionKey(): string
    {
        return $this->providerName . '.oauth2.verifier';
    }
}
