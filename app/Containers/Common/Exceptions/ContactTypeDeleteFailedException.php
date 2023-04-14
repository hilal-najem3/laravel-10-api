<?php

namespace App\Containers\Common\Exceptions;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;

class ContactTypeDeleteFailedException extends ApplicationException
{
    protected $message = '';

    public function __construct()
    {
        $this->message = MessagesHelper::processMessageKey('CONTACT_TYPES.HAS_CONTACTS');
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