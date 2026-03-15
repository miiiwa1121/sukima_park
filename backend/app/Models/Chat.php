<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'CHAT_TABLE';
    protected $primaryKey = 'CHAT_ID';
    public $timestamps = false;

    protected $fillable = [
        'USER_ID_FROM',
        'USER_ID_TO',
        'MESSAGE',
        'IMAGE',
        'YEAR',
        'DATE',
        'TIME',
    ];

    protected $casts = [
        'YEAR' => 'date',
        'DATE' => 'date',
    ];

    // このメッセージの送信者
    public function sender()
    {
        return $this->belongsTo(Member::class, 'USER_ID_FROM', 'USER_ID');
    }

    // このメッセージの受信者
    public function receiver()
    {
        return $this->belongsTo(Member::class, 'USER_ID_TO', 'USER_ID');
    }
}
