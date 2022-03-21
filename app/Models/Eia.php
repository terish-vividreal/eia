<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;

class Eia extends Model
{
    use HasFactory;
    use RevisionableTrait;

    public function stage()
    {
        return $this->belongsTo(EiaStage::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getFormattedDateOfEntryAttribute()
    {
        return FunctionHelper::dateToTimeZone($this->date_of_entry, 'd/m/Y h:i');
    }
}