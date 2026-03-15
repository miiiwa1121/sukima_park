<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'REPLY_TABLE';
    protected $primaryKey = 'REPLY_ID';
    public $timestamps = false;

    protected $fillable = [
        'CONTACT_ID',
        'USER_ID',
        'MESSAGE',
        'DATE',
    ];

    protected $casts = [
        'DATE' => 'date',
    ];

    // この返信の元の問い合わせ
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'CONTACT_ID', 'CONTACT_ID');
    }

    // この返信の送信者
    public function sender()
    {
        return $this->belongsTo(Member::class, 'USER_ID', 'USER_ID');
    }
}
