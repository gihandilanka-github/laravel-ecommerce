<?php

namespace App\Models;

use App\Models\BaseModel;

class Permission extends BaseModel
{
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
