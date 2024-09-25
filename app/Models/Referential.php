<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referential extends Model
{
    use HasFactory;

    // Si votre table s'appelle 'referentiels', cela sera automatiquement détecté,
    // sinon, vous pouvez spécifier le nom de la table.
    protected $table = 'referentiels';

    // Définir les champs qui peuvent être assignés en masse (fillables)
    protected $fillable = [
        'code',
        'libelle',
        'photo',
        'description',
        'photo',
        'statut'
    ];

}
