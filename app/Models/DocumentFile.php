<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    public function getShowFilenameAttribute()
    {
        return ($this->name != '') ? asset('storage/documents/' . $this->name) : asset('admin/images/image-not-found.png');
    }

    public function getFileNameAttribute($name)
    {
        if(pathinfo($this->name, PATHINFO_EXTENSION) == 'pdf'){
            $name = asset('admin/images/demo_pdf.png') ;
        }else{
            $name = ($this->name != null) ? asset('storage/documents/' . $this->name) : asset('admin/images/image-not-found.png');
        }
        return $name;
    }
}
