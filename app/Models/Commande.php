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
        'client_id',
        'client_email',
        'client_nom',
        'client_prenom',
        'client_telephone',
        'client_civilite',
        'client_nom_societe',
        'client_adresse',
        'client_complement_adresse',
        'client_ville',
        'client_code_postal',
        'client_pays',
        'id_api_commande',
        'id_plateforme',
        'total_prix_ttc',
        'statut',
        'details_commande_lignes',
        'invoice_content',
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
     * Get the client that owns the Commande.
     */
    public function client(): BelongsTo // Renamed method to client
    {
        return $this->belongsTo(Client::class, 'client_id'); // Link to Client model, specify foreign key
    }
}
