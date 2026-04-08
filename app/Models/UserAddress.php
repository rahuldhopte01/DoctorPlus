<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_address';

    protected $fillable = ['address', 'postal_code', 'city', 'lat', 'lang', 'user_id', 'label'];

    public function UserAddress()
    {
        return $this->hasOne('App\Models\UserAddress');
    }
}
