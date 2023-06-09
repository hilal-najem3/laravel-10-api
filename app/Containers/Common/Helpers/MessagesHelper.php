<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Common\Messages\Messages as CommonMessages;
use App\Containers\Roles\Messages\Messages as RolesMessages;
use App\Containers\Permissions\Messages\Messages as PermissionsMessages;
use App\Containers\Files\Messages\Messages as FilesMessages;
use App\Containers\Users\Messages\Messages as UsersMessages;
use App\Containers\Auth\Messages\Messages as AuthMessages;
use App\Containers\Agencies\Messages\Messages as AgencyMessages;
use App\Containers\Currencies\Messages\Messages as CurrencyMessages;

class MessagesHelper
{
    public static function messages()
    {
        $commonMessages = CommonMessages::messages();
        $filesMessages = FilesMessages::messages();
        $usersMessages = UsersMessages::messages();
        $authMessages = AuthMessages::messages();
        $rolesMessages = RolesMessages::messages();
        $agencyMessages = AgencyMessages::messages();
        $permissionsMessages = PermissionsMessages::messages();
        $currencyMessages = CurrencyMessages::messages();

        $messages = array_merge($commonMessages, $usersMessages);
        $messages = array_merge($messages, $filesMessages);
        $messages = array_merge($messages, $rolesMessages);
        $messages = array_merge($messages, $permissionsMessages);
        $messages = array_merge($messages, $agencyMessages);
        $messages = array_merge($messages, $authMessages);
        $messages = array_merge($messages, $currencyMessages);

        $messages = array_merge($messages, [
            'NOT_FOUND' => 'is not found',
            'NOT_ALLOWED' => 'You are not authorized to ',
            'CREATE_FAILED' => 'create failed',
            'UPDATE_FAILED' => 'update failed',
            'DELETE_FAILED' => 'delete failed',
            'FILE_SAVE_FAILED' => 'save failed',
            'FILE_GET_FAILED' => 'get failed',
            'ARGUMENT_NULL' => 'can\'t be null',
            'PERMISSION' => 'Permission',
            'ROLE' => 'Role',
        ]);

        return $messages;
    }

    /**
     * This function gets a message key and returns its value
     * 
     * @param string $key
     * @return string $output
     */
    public static function processMessageKey(string $key)
    {
        $output = '';
        if($key != null) {
            $messages = self::messages();
            $keysArray = explode('.', $key);
            $finalArray = self::getMessagesFinalArray($keysArray, $messages);
            $finalKey = $keysArray[count($keysArray) - 1];
            $output = $finalArray[$finalKey] ? $finalArray[$finalKey] : '';
        }
        return $output;
    }

    /**
     * This function returns the final array in messages according
     * to keys array that are a exploded with dot in between
     * 
     * @param array $keysArray
     * @param array $messages
     * @return array $output
     */
    public static function getMessagesFinalArray($keysArray, $messages)
    {
        $output = $messages;
        if(count($keysArray) == 1) {
            return $output;
        }
        for($i = 0; $i < (count($keysArray) - 2); $i++) {
            $current = $keysArray[$i];
            $output = $output[$current];
        }
        $current = $keysArray[$i];
        if(is_array($output[$current])) {
            $output = $output[$current];
        }
        return $output;
    }
}