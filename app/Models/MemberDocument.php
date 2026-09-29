<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\EncryptsRouteKey;

class MemberDocument extends Model
{
    use EncryptsRouteKey;
    protected $fillable = ['member_id', 'title', 'doc_type', 'file_path', 'notes', 'uploaded_by'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
