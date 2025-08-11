<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Cliente;
use App\Models\User;
use App\Models\FacturaDetalle;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $cliente_id
 * @property int $user_id
 * @property float $total
 * @property bool|null $anulada
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property Cliente $cliente
 * @property User $usuario
 * @property \Illuminate\Database\Eloquent\Collection<int, FacturaDetalle> $detalles
 */
class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'subtotal',
        'descuento',
        'iva',
        'total',
        'anulada',
        'pagada',
        'estado',
        'created_by',
    ];

    public function pagos()
    {
        return $this->hasMany(Pagos::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
