<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     * These must match the fields you're importing via Excel
     *
     * @var array
     */
    protected $fillable = [
             // Only include if you want to allow manual ID assignment
        'name',
        'lastname', 
        'age'
    ];

    /**
     * Disable auto-incrementing if manually setting IDs in Excel
     * Only needed if your Excel files contain ID values
     */
    public $incrementing = true;

    /**
     * Cast age to integer (optional)
     */
    protected $casts = [
        'age' => 'integer'
    ];
}