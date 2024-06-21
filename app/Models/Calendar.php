<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calendar extends Model
{
    use HasFactory;

    protected  $fillable = ["title","content","date_time","pin","user_id"];
   
    public function user(){
        return $this->belongsTo(User::class);
      }
}
