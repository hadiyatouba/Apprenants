<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Assurez-vous que cela correspond à la colonne de votre table `roles`
    ];

    /**
     * Relation avec le modèle User.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
