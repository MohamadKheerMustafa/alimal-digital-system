<?php

namespace App\Models\Archive;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'category_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_update_requested',
        'is_delete_requested'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Define the approvmentRequests relationship in the Archive model.
     */
    public function approvmentRequests()
    {
        return $this->belongsToMany(Profile::class, 'approval_requests', 'archive_id', 'profile_id')
            ->withPivot(['request_type', 'status'])
            ->withTimestamps()
            ->using(ApprovalRequests::class);
    }
}
