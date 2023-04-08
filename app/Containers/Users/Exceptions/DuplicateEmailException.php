<?php

namespace App\Containers\Users\Exceptions;

use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;
use App\Containers\Common\Helpers\MessagesHelper;

class DuplicateEmailException extends ApplicationException
{
    protected $messages = array();

    public function __construct()
    {
        $this->messages = MessagesHelper::messages();
    }

    public function status(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function help(): string
    {
        return trans($this->messages['EMAIL_EXISTS']);
    }

    public function error(): string
    {
        return trans($this->messages['EMAIL_EXISTS']);
    }
}