<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewComment extends Model
{
    use HasFactory;

    protected $table = 'REVIEW_COMMENT_TABLE';
    protected $primaryKey = 'REVIEW_COMMENT_ID';
    public $timestamps = false;

    protected $fillable = [
        'LAND_REVIEW',
        'LAND_COMMENT',
        'USER_REVIEW',
        'USER_COMMENT',
        'DATE',
        'USER_ID',
        'LAND_ID',
        'RECORD_ID',
    ];

    protected $casts = [
        'DATE' => 'date',
    ];

    // このレビューを書いた会員
    public function reviewer()
    {
        return $this->belongsTo(Member::class, 'USER_ID', 'USER_ID');
    }

    // このレビューの土地
    public function land()
    {
        return $this->belongsTo(Land::class, 'LAND_ID', 'LAND_ID');
    }

    // このレビューの貸出記録
    public function rentalRecord()
    {
        return $this->belongsTo(RentalRecord::class, 'RECORD_ID', 'RECORD_ID');
    }
}
