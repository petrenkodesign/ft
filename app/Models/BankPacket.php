<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPacket extends Model
{
  protected $fillable = [
      'name', 'surname', 'birthday', 'total', 'comment'
  ];
    use HasFactory;
}
