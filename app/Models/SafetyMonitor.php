<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyMonitor extends Model
{
    use HasFactory;

    public function isSafe(){
        return false;
    }
}
