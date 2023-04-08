<?php

namespace App\Messages;

trait ExceptionMessages
{
    public function messages()
    {
        return [
            'NOT_FOUND' => 'is not found',
            'NOT_ALLOWED' => 'You are not authorized to ',
            'CREATE_FAILED' => 'create failed',
            'UPDATE_FAILED' => 'update failed',
            'DELETE_FAILED' => 'delete failed',
            'FILE_SAVE_FAILED' => 'save failed',
            'FILE_GET_FAILED' => 'get failed',
            'ARGUMENT_NULL' => 'can\'t be null'
        ];
    }
}