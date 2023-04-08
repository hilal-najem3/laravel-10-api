<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Common\Messages\Messages as CommonMessages;
use App\Containers\Users\Messages\Messages as UsersMessages;
use App\Containers\Auth\Messages\Messages as AuthMessages;

class MessagesHelper
{
    public static function messages()
    {
        $commonMessages = CommonMessages::messages();
        $usersMessages = UsersMessages::messages();
        $authMessages = AuthMessages::messages();

        $messages = array_merge($commonMessages, $usersMessages);
        $messages = array_merge($messages, $authMessages);

        $messages = array_merge($messages, [
            'NOT_FOUND' => 'is not found',
            'NOT_ALLOWED' => 'You are not authorized to ',
            'CREATE_FAILED' => 'create failed',
            'UPDATE_FAILED' => 'update failed',
            'DELETE_FAILED' => 'delete failed',
            'FILE_SAVE_FAILED' => 'save failed',
            'FILE_GET_FAILED' => 'get failed',
            'ARGUMENT_NULL' => 'can\'t be null'
        ]);

        return $messages;
    }
}