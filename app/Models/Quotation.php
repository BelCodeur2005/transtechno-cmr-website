<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Quotation extends Model {
    protected $fillable = ["reference","status","priority","responsible_name","structure_name","city","sector","phone","specifications","is_urgent","services_installation","services_formation","services_maintenance","delivery_city","transport_cost","discount_percent","vat_exempt","internal_notes","assigned_to"];
    protected $casts = ["is_urgent" => "boolean","services_installation" => "boolean","services_formation" => "boolean","services_maintenance" => "boolean","vat_exempt" => "boolean"];
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, "assigned_to"); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
    public function subtotalHt(): int { return $this->items->sum(fn($item) => $item->quantity * $item->unit_price_fcfa); }
    public function totalTtc(): int {
        $subtotal = $this->subtotalHt();
        $afterDiscount = $subtotal - ($subtotal * $this->discount_percent / 100);
        $afterTransport = $afterDiscount + $this->transport_cost;
        $tva = $this->vat_exempt ? 0 : $afterTransport * 0.1925;
        return (int) round($afterTransport + $tva);
    }
}