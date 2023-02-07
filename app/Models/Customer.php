<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

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
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('D M d Y h:m:s T+0200');
    }
}
