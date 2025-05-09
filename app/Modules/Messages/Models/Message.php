<?php

namespace App\Modules\Messages\Models;

use App\Models\User;
use App\Modules\Attachments\Models\Attachment;
use App\Modules\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    public static function rules()
    {
        return [
            'message'     => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // max 5MB each
        ];
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
