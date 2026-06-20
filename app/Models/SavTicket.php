<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SavTicket extends Model {
    protected $fillable = [
        'reference', 'client_id', 'description', 'equipments_notes', 'pre_diagnostic',
        'status', 'priority', 'assigned_technician_id',
        'opened_at', 'resolved_at', 'rapport_intervention',
    ];

    protected $casts = [
        'opened_at'   => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (SavTicket $ticket) {
            if (! empty($ticket->reference)) {
                return;
            }
            $year  = now()->year;
            $count = static::where('reference', 'like', "SAV-{$year}-%")->count();
            do {
                $count++;
                $ref = sprintf('SAV-%d-%03d', $year, $count);
            } while (static::where('reference', $ref)->exists());
            $ticket->reference = $ref;
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** Équipements du parc concernés par ce ticket (plusieurs possibles). */
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(
            EquipmentInventory::class,
            'sav_ticket_equipment',
            'sav_ticket_id',
            'equipment_inventory_id'
        );
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }
}
