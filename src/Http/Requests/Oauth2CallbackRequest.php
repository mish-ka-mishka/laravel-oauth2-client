<?php

namespace LaravelOAuth2Client\Http\Requests;

use Illuminate\Http\Request;

/**
 * @property-read string $code
 * @property-read string $state
 */
class OAuth2CallbackRequest extends Request
{
}
