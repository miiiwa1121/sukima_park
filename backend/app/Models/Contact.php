<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'CONTACT_TABLE';
    protected $primaryKey = 'CONTACT_ID';
    public $timestamps = false;

    protected $fillable = [
        'TITLE',
        'MESSAGE',
        'USER_ID',
        'DATE',
        'STATUS',
    ];

    protected $casts = [
        'DATE' => 'date',
    ];

    // この問い合わせの送信者
    public function sender()
    {
        return $this->belongsTo(Member::class, 'USER_ID', 'USER_ID');
    }

    // この問い合わせへの返信
    public function replies()
    {
        return $this->hasMany(Reply::class, 'CONTACT_ID', 'CONTACT_ID');
    }
}
