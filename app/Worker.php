<?php

namespace App;

use App\Delegation\Delegation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperWorker
 */
class Worker extends Model
{
    use HasFactory;

    public function delegations(): HasMany
    {
        return $this->hasMany(Delegation::class);
    }
}
