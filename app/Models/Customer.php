<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    public function fromCustomer()
    {
        return $this->hasMany(Transfer::class, 'from_customer');
    }
    public function toCustomer()
    {
        return $this->hasMany(Transfer::class, 'to_customer');
    }
}
