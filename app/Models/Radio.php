<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radio extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'title_id';
    public function getRouteKeyName()
{
    return 'title_id';
}
    /**
     * The attributes that are mass assignable.
     * These must match the fields you're importing via Excel
     *
     * @var array
     */
    protected $fillable = [
             // Only include if you want to allow manual ID assignment
        'title_id',
        'title', 
        'soundfile_name',
        'author',
        'durée(ms)',
        'Durée',
        'interpret',
        'last_modif_time',
        'commentaire1',
        'commentaire2',
        'commentaire3'
    ];

    /**
     * Disable auto-incrementing if manually setting IDs in Excel
     * Only needed if your Excel files contain ID values
     */
    public $incrementing = false;

    /**
     * Cast age to integer (optional)
     */
    protected $casts = [
        "durée(ms)"=>"integer",
        'Durée' => 'string', // or 'date' if it only contains date without time
        'last_modif_time' => 'string', // or 'date' if it only contains date without time
    // add other date fields here as necessary
    ];
}