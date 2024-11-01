<?php

namespace App\Models\Archive;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'parent_id'];

    /**
     * Get the shortcut for the category name.
     *
     * @return string
     */
    public function getShortcutAttribute()
    {
        // Generate a shortcut from the category name
        return strtolower(str_replace(' ', '_', $this->name));
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
