<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportArticleFeedback extends Model
{
    protected $table = 'support_article_feedback';

    protected $fillable = [
        'article_id', 'is_helpful', 'ip_address', 'user_id', 'comment',
    ];

    protected function casts(): array
    {
        return ['is_helpful' => 'boolean'];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(SupportArticle::class, 'article_id');
    }
}
