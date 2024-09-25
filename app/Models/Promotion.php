<?php

namespace App\Models;

use App\Models\Referential;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'duration',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function referentials()
    {
        return $this->belongsToMany(Referential::class);
    }

    // public function students()
    // {
    //     return $this->belongsToMany(Student::class);
    // }
}
