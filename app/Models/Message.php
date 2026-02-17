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

    private function mapTableToModel($table)
    {
        $map = [
            'orders' => 'App\Models\Order',
            // Add more mappings as needed
        ];

        return $map[$table] ?? $table;
    }
}
