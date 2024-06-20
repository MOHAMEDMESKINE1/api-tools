<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskItem extends Model
{
    use HasFactory;
    protected  $fillable = ["done","content","task_id"];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
