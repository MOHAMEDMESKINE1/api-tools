<?php

namespace App\Models;

use App\Models\TaskItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    protected  $fillable = ["title","pin","user_id"];
    
    public function taskItems()
    {
        return $this->hasMany(TaskItem::class);
    }
}
