<?php

namespace App\Containers\Common\Exceptions;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;

class ContactTypeDuplicateNameException extends ApplicationException
{
    protected $message = '';

    public function __construct()
    {
        $this->message = MessagesHelper::processMessageKey('CONTACT_TYPES.DUPLICATE_NAME');
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