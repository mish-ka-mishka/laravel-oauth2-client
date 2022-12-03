<?php

namespace LaravelOauth2Client\Http\Requests;

use Illuminate\Http\Request;

/**
 * @property-read string $code
 * @property-read string $state
 */
class Oauth2CallbackRequest extends Request
{
}
