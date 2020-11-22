<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vendor extends Model
{
    use Notifiable;

    protected $table ='vendors';  

    protected $fillable = [
        'name', 'mobile','password','address', 'email','category_id','active','created_at','updated_at','active','logo'
    ]; 

    protected $hidden = ['category_id','password']; 

    public function scopeActive($query) {
        return $query ->where('active',1);  
    }

    public function getLogoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }

    public function getActive()
    {
       return $this->active == 1 ? 'مفعل ' : ' مفعل غير';

    }

    public function scopeSelection($query)
    {
        return $query->select('id','category_id','name','logo','email','mobile'); 
    }

    public function category()
    {
        return $this->belongsTo('App\Models\MainCategory','category_id','id');
    }


    public function setPasswordAttribute($password)
    {
        if(!empty($password)) {
            $this->attributes['password'] = bcrypt($password); 
        }
    }

}