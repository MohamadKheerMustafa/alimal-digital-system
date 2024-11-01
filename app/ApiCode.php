<?php

namespace App;

class ApiCode
{
    public const SUCCESS = 200;

    public const CREATED = 201;

    public const SOMETHING_WENT_WRONG = 250;
    public const VERIFICATION_FAILED = 251;

    public const ITEM_LIMIT_EXCEEDED = 252;
    public const SPACIAL_ADVERT_LIMIT_EXCEEDED = 253;
    public const POSTS_LIMIT_EXCEEDED = 254;

    public const BAD_REQUEST = 400;

    public const UNAUTHORIZED = 401;

    public const NOT_FOUND = 404;

    public const FORBIDDEN = 403;
    public const METHOD_NOT_ALLOWED = 405;
    public const INTERNAL_SERVER_ERROR = 500;
}
