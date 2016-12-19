<?php
/**
 * Created by PhpStorm.
 * User: pavan
 * Date: 18/12/2016
 * Time: 11:51 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nid',
        'checked',
        'content',
        '_constructedStringLength',
        'read'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * To specify that no timestamp columns exist
     *
     * @var boolean
     */
    public $timestamps = false;
}