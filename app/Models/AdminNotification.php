<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminNotification
 *
 * This model represents an admin notification.
 *
 * @property int $id
 * @property string $type The type of notification (e.g., 'fraud').
 * @property string $message The message content of the notification.
 * @property \Carbon\Carbon $created_at The time the notification was created.
 * @property \Carbon\Carbon $updated_at The time the notification was last updated.
 */
class AdminNotification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'message',
    ];
}
