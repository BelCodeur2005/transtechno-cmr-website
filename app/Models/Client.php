<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Client extends Model {
    protected $fillable = ["structure_name","city","sector","email","phone","is_validated","user_id"];
    protected $casts = ["is_validated" => "boolean"];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function contacts(): HasMany { return $this->hasMany(ClientContact::class); }
    public function quotations(): HasMany { return $this->hasMany(Quotation::class); }
    public function savTickets(): HasMany { return $this->hasMany(SavTicket::class); }
    public function equipmentInventory(): HasMany { return $this->hasMany(EquipmentInventory::class); }
}