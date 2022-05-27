<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','employee_id','title','description'];

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

    public function employee()
    {
      return $this->belongsTo(Employee::class);
    }
}
