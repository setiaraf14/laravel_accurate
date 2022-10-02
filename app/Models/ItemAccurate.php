<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAccurate extends Model
{
    use HasFactory;
    protected $table = 'item_accurates';
    protected $guarded = [];
}
