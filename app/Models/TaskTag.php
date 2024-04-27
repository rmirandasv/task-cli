<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskTag extends Pivot
{
    use HasFactory;

    protected $fillable = ['task_id', 'tag_id'];
}
