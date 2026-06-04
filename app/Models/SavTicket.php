<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SavTicket extends Model {
    protected $fillable = ["reference","client_id","equipment_id","description","status","priority","assigned_technician_id","opened_at","resolved_at"];
    protected $casts = ["opened_at" => "datetime","resolved_at" => "datetime"];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function equipment(): BelongsTo { return $this->belongsTo(EquipmentInventory::class, "equipment_id"); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, "assigned_technician_id"); }
}