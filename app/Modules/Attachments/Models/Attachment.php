<?php

namespace App\Modules\Attachments\Models;

use App\Modules\Messages\Models\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [
        'message_id',
        'file_path',
        'original_name',
    ];

    public static function rules($productId = null)
    {
        return [
            'name' => 'required|string|max:191|unique:products,name,' . $productId,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
