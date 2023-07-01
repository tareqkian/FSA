<?php

namespace Tarek\Fsa\Traits;

use Illuminate\Contracts\Auth\MustVerifyEmail;

trait CheckEmailVerifyImplementation {
    public function isMustVerifyEmail(): bool
    {
        return ($this instanceof MustVerifyEmail);
    }
}
