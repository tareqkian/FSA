<?php

namespace Tarek\Fsa\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Tarek\Fsa\Models\Provider;

trait Providers {
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class,'user_id','id');
    }
}
