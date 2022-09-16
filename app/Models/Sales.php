<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['customer', 'item'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function item(){
        return $this->belongsTo(Item::class);
    }
}
