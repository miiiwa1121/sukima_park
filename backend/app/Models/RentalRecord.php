<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalRecord extends Model
{
    use HasFactory;

    protected $table = 'RENTAL_RECORD_TABLE';
    protected $primaryKey = 'RECORD_ID';
    public $timestamps = false;

    protected $fillable = [
        'PRICE',
        'PRICE_UNIT',
        'RENTAL_START_DATE',
        'RENTAL_END_DATE',
        'RENTAL_START_TIME',
        'RENTAL_END_TIME',
        'LAND_ID',
        'USER_ID',
    ];

    protected $casts = [
        'RENTAL_START_DATE' => 'date',
        'RENTAL_END_DATE' => 'date',
        // 時刻カラムはCarbonにキャストしてビュー側のformat()を安全に利用する
        'RENTAL_START_TIME' => 'datetime:H:i:s',
        'RENTAL_END_TIME' => 'datetime:H:i:s',
    ];

    // この記録の土地
    public function land()
    {
        return $this->belongsTo(Land::class, 'LAND_ID', 'LAND_ID');
    }

    // この記録のレンタル者
    public function renter()
    {
        return $this->belongsTo(Member::class, 'USER_ID', 'USER_ID');
    }

    // この記録のレビュー
    public function review()
    {
        return $this->hasOne(ReviewComment::class, 'RECORD_ID', 'RECORD_ID');
    }
}
