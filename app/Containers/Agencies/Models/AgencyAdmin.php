<?php

namespace App\Containers\Agencies\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyAdmin extends Model
{
    // This is a model the represents agency admin aka user and agency id
    // which means a user if he has the role agency-admin, he can update data for that agency
    // Such as agency's default currency, ...
}
