<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;

class Document extends Model
{
    use HasFactory, RevisionableTrait;

    public function eia()
    {
        return $this->belongsTo(Eia::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id', 'id')->orderBy('id', 'DESC');
    }

    public function getFormattedDateOfEntryAttribute()
    {
        return FunctionHelper::dateToTimeZone($this->date_of_entry, 'd/m/Y h:i');
    }

    public function files()
    {
        return $this->hasMany(DocumentFile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function stage()
    {
        return $this->belongsTo(EiaStage::class);
    }

    public function latestFile()
    {
        return $this->hasOne(DocumentFile::class)->latestOfMany();
    }   

    public function assign()
    {
        return $this->belongsTo(TaskAssign::class, 'id', 'document_id');
    }
}