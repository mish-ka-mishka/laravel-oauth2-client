<?php

namespace LaravelOAuth2Client\Tests;

use LaravelOAuth2Client\OAuth2Service;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use Psr\Http\Message\ResponseInterface;

class OAuth2ServiceTest extends TestCase
{
    /**
     * Test the guessProviderName method with concrete test classes
     */
    public function testGuessProviderName()
    {
        $testCases = [
            [new FakeProvider([]), 'fake'],
            [new FakePr([]), 'fakepr'],
            [new ProviderFake([]), 'providerfake'],
        ];

        foreach ($testCases as [$provider, $expected]) {
            $result = OAuth2Service::guessProviderName($provider);

            $this->assertEquals($expected, $result, "Failed asserting that '$result' equals '$expected' for class '" . get_class($provider) . "'");
        }
    }
}

// Concrete test classes for OAuth2ServiceTest

// Test class with 'Provider' suffix
class FakeProvider extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
        return '';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return '';
    }

    public function getResourceOwnerDetailsUrl($token)
    {
        return '';
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
    }

    protected function createResourceOwner(array $response, $token)
    {
        return new GenericResourceOwner($response, 'id');
    }
}

// Test class without 'Provider' suffix
class FakePr extends FakeProvider {}

// Test class with 'Provider' in the beginning of the name
class ProviderFake extends FakeProvider {}
