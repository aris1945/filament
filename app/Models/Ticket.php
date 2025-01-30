<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Ticket extends Model
{
    protected $fillable = [
      'ticket_number',
      'category',
      'subcategory',
      'assigned_to',
      'evident_image',
      'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'nik');
    }
}
