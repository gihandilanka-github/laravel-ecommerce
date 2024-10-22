<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use SerializeDate;
    protected $guarded = ['id'];
}
