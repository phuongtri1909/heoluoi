<?php

namespace App\Models;

use App\Models\CommentReaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment',
        'reply_id',
        'level',
        'is_pinned',
        'pinned_at',
        'story_id',
        'approval_status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($comment) {
            if ($comment->reply_id) {
                $parentComment = Comment::find($comment->reply_id);
                $comment->level = $parentComment ? $parentComment->level + 1 : 0;
            }
        });
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_id')->where('level', '<', 3);
    }
    
    public function approvedReplies()
    {
        return $this->hasMany(Comment::class, 'reply_id')->where('level', '<', 3)->approved();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'reply_id');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function likes()
    {
        return $this->reactions()->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->reactions()->where('type', 'dislike');
    }

    // Add scope for pinned comments
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true)->orderBy('pinned_at', 'desc');
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Add scope for approved comments
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }
}
