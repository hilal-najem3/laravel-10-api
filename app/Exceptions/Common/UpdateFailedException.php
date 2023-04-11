<?php

namespace App\Exceptions\Common;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;

class UpdateFailedException extends ApplicationException
{
    protected $messages = array();
    protected $name = '';
    protected $message = '';

    public function __construct($name = '')
    {
        $message = MessagesHelper::processMessageKey($name);
        $exceptionMessage = MessagesHelper::processMessageKey('UPDATE_FAILED');
        $this->message = $message . ' ' . $exceptionMessage;
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