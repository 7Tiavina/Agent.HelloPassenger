<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'email',
        'password_hash',
        'nom',
        'prenom',
        'telephone',
        'civilite',
        'nomSociete',
        'adresse',
        'complementAdresse',
        'ville',
        'codePostal',
        'pays',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
