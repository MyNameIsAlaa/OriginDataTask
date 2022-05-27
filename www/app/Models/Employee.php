<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','first_name','last_name','phone', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    public function company()
    {
      return $this->belongsTo(Company::class);
    }


    public function projects()
    {
      return $this->hasMany(Project::class);
    }


}
