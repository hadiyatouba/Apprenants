<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'login',
        'photo',
        'fonction',
        'statut',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'firebase_uid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relation avec le modèle Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope a query to only include users of a given role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $roleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRole(Builder $query, int $roleId): Builder
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope a query to only include users with a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('statut', $status);
    }


   // Relation Many-to-Many avec Promotion
   public function promotions()
   {
       return $this->belongsToMany(Promotion::class, 'promotion_user');
   }

    // Relation avec le modèle Referentiel
    public function referentiels()
    {
        return $this->hasMany(Referentiel::class);
    }



    
}
