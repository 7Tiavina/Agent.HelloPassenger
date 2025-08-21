<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commande extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'commandes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'id_api_commande',
        'id_plateforme',
        'client_email',
        'client_telephone',
        'client_nom',
        'client_prenom',
        'client_civilite',
        'client_nom_societe',
        'client_adresse',
        'client_complement_adresse',
        'client_ville',
        'client_code_postal',
        'client_pays',
        'total_prix_ttc',
        'statut',
        'details_commande_lignes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'details_commande_lignes' => 'array',
        'total_prix_ttc' => 'decimal:2',
    ];

    /**
     * Get the user that owns the Commande.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
