<?php

namespace App\Exceptions\Common;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;

class NotAllowedException extends ApplicationException
{
    protected $message = '';

    public function __construct($name = '', $customKey = null)
    {
        if($customKey != null) {
            $this->message = MessagesHelper::processMessageKey($customKey);
        } else {
            $message = MessagesHelper::processMessageKey($name);
            $exceptionMessage = MessagesHelper::processMessageKey('NOT_ALLOWED');
            $this->message = $exceptionMessage . ' ' . $message;
        }
    }

    public function status(): int
    {
        return Response::HTTP_METHOD_NOT_ALLOWED;
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