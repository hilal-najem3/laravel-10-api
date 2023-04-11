<?php

namespace App\Containers\Users\Exceptions;

use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;
use App\Containers\Common\Helpers\MessagesHelper;

class UpdatePasswordFailedException extends ApplicationException
{
    protected $message = '';

    public function __construct()
    {
        $this->message = MessagesHelper::processMessageKey('PROFILE.PASSWORD_ERROR');
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