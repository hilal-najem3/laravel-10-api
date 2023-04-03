<?php

namespace App\Containers\Common\Messages;

trait Messages
{
    public function messages()
    {
        return [
            'DATA' => [
                'EXCEPTION' => 'Data'
            ],
            "REGIONS" => [
                "ALL" => 'Countries and region types loaded successfully',
                "ALL_FAILED" => 'Countries and region types load failed'
            ]
        ];
    }
}
