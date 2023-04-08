<?php

namespace App\Exceptions\Common;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Response;

class ArgumentNullException extends ApplicationException
{
    protected $messages = array();
    protected $name = '';
    protected $message = '';

    public function __construct($name = '')
    {
        $this->name = $name;
        $this->messages = MessagesHelper::messages();
        $this->message = $this->name == '' ? '' : $this->name . ' ';
        $this->message .= $this->messages['ARGUMENT_NULL'];
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