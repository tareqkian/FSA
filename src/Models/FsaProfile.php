<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsaProfile extends Model
{

  protected $primaryKey = 'user_id';

  protected $fillable = [
    "user_id",
    "first_name",
    "last_name",
    "birthday",
    "gender",
    "address",
    "city",
    "state",
    "postal_code",
    "country",
    "locale"
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
