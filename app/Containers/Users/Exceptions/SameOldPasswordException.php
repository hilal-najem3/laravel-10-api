<?php

namespace App\Containers\Users\Exceptions;

use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;
use App\Containers\Common\Helpers\MessagesHelper;

class SameOldPasswordException extends ApplicationException
{
    protected $message = '';

    public function __construct()
    {
        $this->message = MessagesHelper::processMessageKey('PROFILE.OLD_PASSWORD_ERROR_EQUAL_NEW');
    }

    public function status(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function help(): string
    {
        return trans($this->message);
    }

    public function error(): string
    {
        return trans($this->message);
    }
}