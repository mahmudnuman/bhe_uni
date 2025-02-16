<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['assignment_id', 'status'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
    
    public function lead()
    {
        return $this->hasOneThrough(Lead::class, Assignment::class, 'id', 'id', 'assignment_id', 'lead_id');
    }
    
    public function counselor()
    {
        return $this->hasOneThrough(User::class, Assignment::class, 'id', 'id', 'assignment_id', 'counselor_id');
    }
}
