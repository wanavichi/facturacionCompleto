<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagos extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'tipo_pago',
        'numero_transaccion',
        'monto',
        'observacion',
        'pagado_por',
        'validado_por',
        'validated_at',
        'estado',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function pagador()
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
}
