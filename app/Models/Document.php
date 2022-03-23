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

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }

    public function stage()
    {
        return $this->belongsTo(EiaStage::class);
    }

    public function latestFile()
    {
        return $this->hasOne(DocumentFile::class)->latestOfMany();
    }
    
}
