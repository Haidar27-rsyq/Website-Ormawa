<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota_Agenda extends Model
{
    use HasFactory;
    protected $table = 'anggota_agenda';
    protected $guarded = ['id'];

}
