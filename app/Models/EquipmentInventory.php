<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class EquipmentInventory extends Model {
    protected $fillable = ["client_id","product_id","serial_number","installation_date","warranty_expiry","last_maintenance_date","notes"];
    protected $casts = ["installation_date" => "date","warranty_expiry" => "date","last_maintenance_date" => "date"];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function savTickets(): HasMany { return $this->hasMany(SavTicket::class, "equipment_id"); }
}