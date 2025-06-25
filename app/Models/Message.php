<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from_role', 'from_id', 'to_role', 'to_id','conversation_id' ,'content', 'type', 'is_read',
    ];

   public function sinhvienSender()
{
    return $this->belongsTo(SinhVien::class, 'from_id');
}
   public function AdminSender()
{
    return $this->belongsTo(Admin::class, 'from_id');
}


    public function receiver()
    {
        if ($this->to_role === 'sinhvien') {
            return $this->belongsTo(SinhVien::class, 'to_id');
        } elseif ($this->to_role === 'admin') {
            return $this->belongsTo(Admin::class, 'to_id');
        }
        return null;
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
public function conversation()
{
    return $this->belongsTo(Conversation::class);
}


}
