<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'message',
        'type',
        'media',
        'user_id',
        'messagable_id',
        'messagable_type'
    ];

    /**
     * Get the parent messagable model.
     */
    public function messagable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the creator of the message (alias for creators logic).
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes for backward compatibility or ease of use
    public function scopeVersion1($query, $table, $idRow)
    {
        if (trim($table) != "") {
            // Map table names to model classes if necessary
            $type = $this->mapTableToModel($table);
            $query->where("messagable_type", $type);
        }
        if (trim($idRow) != "") {
            $query->where("messagable_id", $idRow);
        }
    }

    const TABLE_MAP = [
        'orders' => 'App\Models\Order',
        'contacts' => 'App\Models\Contact',
        'exams' => 'App\Models\Exam',
        'sales' => 'App\Models\Sale',
        'payments' => 'App\Models\Payment',
    ];

    const SYSTEM_MESSAGES = [
        'App\Models\Contact' => [
            'created' => [
                'Se ha registrado un nuevo contacto: {name}.',
                'Bienvenido al sistema, contacto {name} creado.',
                'Nuevo registro de contacto: {name}.',
                'El contacto {name} ha sido dado de alta.',
                'Registro exitoso del contacto {name}.'
            ],
            'updated' => [
                'Se actualizaron los datos del contacto {name}.',
                'El contacto {name} ha sido modificado.',
                'Cambios guardados para el contacto {name}.',
                'Información del contacto {name} actualizada.',
                'Actualización reciente en el contacto {name}.'
            ],
            'deleted' => [
                'El contacto {name} ha sido eliminado.',
                'Se ha borrado el contacto {name}.',
                'Contacto {name} eliminado del sistema.',
                'Baja del contacto {name}.',
                'El registro de {name} ya no está activo.'
            ]
        ],
        'App\Models\Order' => [
            'created' => [
                'Se creó una nueva orden con folio {id}.',
                'Orden #{id} registrada exitosamente.',
                'Nueva orden generada: #{id}.',
                'El sistema ha registrado la orden #{id}.',
                'Alta de orden #{id} completada.'
            ],
            'updated' => [
                'La orden #{id} ha sido actualizada.',
                'Se modificaron los detalles de la orden #{id}.',
                'Cambios aplicados a la orden #{id}.',
                'Actualización en la orden #{id}.',
                'La orden #{id} tiene nuevos datos.'
            ],
            'deleted' => [
                'La orden #{id} ha sido eliminada.',
                'Se canceló/eliminó la orden #{id}.',
                'Baja de la orden #{id}.',
                'Orden #{id} eliminada del sistema.',
                'El registro de la orden #{id} fue borrado.'
            ]
        ],
        'App\Models\Sale' => [
            'created' => [
                'Se registró una nueva venta con ID {id}.',
                'Venta #{id} creada en el sistema.',
                'Nueva transacción de venta: #{id}.',
                'Se ha generado la venta #{id}.',
                'Alta de venta #{id}.'
            ],
            'updated' => [
                'La venta #{id} fue actualizada.',
                'Se modificó la venta #{id}.',
                'Cambios en la venta #{id}.',
                'Actualización de datos en venta #{id}.',
                'La venta #{id} ha sufrido cambios.'
            ],
            'deleted' => [
                'Se eliminó la venta #{id}.',
                'Venta #{id} borrada del sistema.',
                'La venta #{id} ha sido dada de baja.',
                'Cancelación/Eliminación de venta #{id}.',
                'Registro de venta #{id} eliminado.'
            ]
        ],
        'App\Models\Payment' => [
            'created' => [
                'Se registró un nuevo pago con ID {id}.',
                'Pago #{id} ingresado al sistema.',
                'Nuevo abono registrado: #{id}.',
                'Se ha creado el pago #{id}.',
                'Alta de pago #{id} exitosa.'
            ],
            'updated' => [
                'El pago #{id} ha sido actualizado.',
                'Modificación en el pago #{id}.',
                'Se actualizaron los datos del pago #{id}.',
                'Cambios en el registro de pago #{id}.',
                'Pago #{id} modificado.'
            ],
            'deleted' => [
                'El pago #{id} ha sido eliminado.',
                'Se borró el pago #{id}.',
                'Baja del pago #{id}.',
                'Pago #{id} eliminado del historial.',
                'Registro de pago #{id} suprimido.'
            ]
        ]
    ];

    private function mapTableToModel($table)
    {
        return self::TABLE_MAP[$table] ?? $table;
    }
}
