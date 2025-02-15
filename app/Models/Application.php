<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'counselor_id', 'status'];

    public function lead() {
        return $this->belongsTo(Lead::class);
    }

    public function counselor() {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
