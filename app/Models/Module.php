<?php

namespace App\Models;

use App\Models\BaseModel;

class Module extends BaseModel
{
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }
}
