<?php

namespace App\Helpers\Response;

use App\Containers\Common\Helpers\MessagesHelper;
use App\Helpers\Response\ResponseJsonErrorReturn;
use Exception;

trait ResponseHelper
{
    public int $success = 200;

    public int $bad_request = 400;

    public int $unauthorized = 401;

    public int $forbidden = 403;

    public int $not_found = 404;

    public int $not_allowed = 405;

    /**
     * This function fills the response that should be sent to the user
     * then returns a response for it.
     * This is used for success returns only
     * used as a minified version of return_response() function
     * 
     * @param  string|null $messageKey response error message key
     * @param  mixed       $data  data returned
     * 
     * @return \Illuminate\Http\Response
     */
    public function response(string $messageKey = null, mixed $data = null)
    {
        return $this->return_response($this->success, $data, $messageKey);
    }

    /**
     * This function fills the response that should be sent to the user
     * then returns a response for it.
     * This is used for error returns only
     * used as a minified version of return_response() function
     * 
     * @param  int         $status  status of the error
     * @param  string|null $messageKey response error message key
     * @param  Exception $e   Exception
     * 
     * @return \Illuminate\Http\Response
     */
    public function errorResponse(int $status, string $messageKey = null, Exception $exception = null)
    {
        return $this->return_response($status, null, $messageKey, $exception);
    }

    /**
     * This function fills the response that should be sent to the user
     * then returns a response for it.
     * 
     * @param  int         $status  status of the error
     * @param  mixed       $data  data returned
     * @param  string|null $messageKey response error message key
     * @param  Exception $e   Exception
     * 
     * @return \Illuminate\Http\Response
     */
    public function return_response(int $status, mixed $data, string $messageKey = null, Exception $exception = null)
    {
        $messages = MessagesHelper::messages();
        $message = $messages[$messageKey] ? $messages[$messageKey] : '';

        $error = $exception ? $this->exception_message($exception) : '';

        if($status != 200) {
            return ResponseJsonErrorReturn::returnErrorResponse($status, $message, $error);
        }

        $data['status'] = 'success';

        if($message != null) {
            $data['message'] = $message;
        }

        return response()->json($data, 200);
    }

    /**
     * This function, receives an exception, 
     * checks if it is a general exception or a custom exception
     * builds a message and returns it
     * 
     * @param Exception $e
     * @return string $message
     */
    public function exception_message(Exception $e)
    {
        try {
            if($e->getMessage() != null && $e->getMessage() != '') {
                return $e->getMessage();
            }
            if($e->getMessage() == '') {
                // We have a normal exception
                return $e->error();
            }
        } catch (Exception $exception) {
            return $e->getMessage();
        }
        return $e->getMessage();
    }

    /**
     * This function, receives data that are paginated by laravel
     * Then it prettifies/customizes the data and returns it
     * 
     * @param $data
     * @return $data
     */
    public function metaData($data)
    {
        return [
            'current_page' => $data->current_page,
            'first_page_url' => $data->first_page_url,
            'from' => $data->from,
            'last_page' => $data->last_page,
            'last_page_url' => $data->last_page_url,
            'next_page_url' => $data->next_page_url,
            'path' => $data->path,
            'per_page' => $data->per_page,
            'prev_page_url' => $data->prev_page_url,
            'to' => $data->to,
            'total' => $data->total,
        ];
    }
}