<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use App\Models\User;
use Usamamuneerchaudhary\Commentify\Traits\Commentable;
use Usamamuneerchaudhary\Commentify\Traits\HasUserAvatar;

class Image extends Model
{
    use HasFactory, Commentable, HasUserAvatar;

    // Các thuộc tính có thể được gán hàng loạt
    protected $fillable = [
        'filename',
        'path',
        'category',
        'description',
        'user_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_images');
    }
    public function getImageUrlAttribute(): string
    {
         return asset($this->path);
    }
}
