<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
    class Channel extends Eloquent
    {
     use UserTrait, RemindableTrait;

 public $timestamps=false;
 
 protected $table = 'channel';

 /**
  * The attributes excluded from the model's JSON form.
  *
  * @var array
  */
  protected $hidden = array('password', 'remember_token');

  protected $primaryKey = 'channel_id';

}