<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;

class Document extends Model
{
    use HasFactory, RevisionableTrait;

    public function getFormattedDateOfEntryAttribute()
    {
        return FunctionHelper::dateToTimeZone($this->date_of_entry, 'd/m/Y h:i');
    }
    
}
