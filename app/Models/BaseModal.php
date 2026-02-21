<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModal extends Model
{
    // column constants
    const ID = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
