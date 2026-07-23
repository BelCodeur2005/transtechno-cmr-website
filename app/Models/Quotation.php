<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Quotation extends Model {
    protected $fillable = ["reference","status","priority","responsible_name","structure_name","city","sector","email","phone","specifications","is_urgent","services_installation","service_installation_cost","services_formation","service_formation_cost","services_maintenance","service_maintenance_cost","delivery_city","transport_cost","discount_percent","vat_exempt","internal_notes","assigned_to","client_id"];
    protected $casts = ["is_urgent" => "boolean","services_installation" => "boolean","services_formation" => "boolean","services_maintenance" => "boolean","vat_exempt" => "boolean"];
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, "assigned_to"); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class)->orderBy('sort_order'); }
    public function statusHistory(): HasMany { return $this->hasMany(QuotationStatusHistory::class)->latest(); }
    public function subtotalHt(): int { return $this->items->sum(fn($item) => $item->quantity * $item->unit_price_fcfa); }
    public function servicesCost(): int { return (int) (($this->services_installation ? $this->service_installation_cost : 0) + ($this->services_formation ? $this->service_formation_cost : 0) + ($this->services_maintenance ? $this->service_maintenance_cost : 0)); }
    public function totalTtc(): int {
        $subtotal = $this->subtotalHt();
        $afterDiscount = $subtotal - (int) round($subtotal * $this->discount_percent / 100);
        $base = $afterDiscount + $this->servicesCost() + $this->transport_cost;
        $tva = $this->vat_exempt ? 0 : $base * 0.1925;
        return (int) round($base + $tva);
    }
}