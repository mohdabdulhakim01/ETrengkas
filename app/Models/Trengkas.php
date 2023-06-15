<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trengkas extends Model
{
    protected $table = 'trengkas';
    protected $guarded = [];
    public $timestamps = false;
    use HasFactory;
}
