<?php

namespace Tarek\Fsa\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Tarek\Fsa\Models\FsaProfile as FsaProfileModel;

trait FsaProfile {
  public function fsa_profile(): HasOne
  {
    return $this->hasOne(FsaProfileModel::class, 'user_id', 'id')
      ->withDefault();
  }
}
