<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : Post.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for post mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $connection = 'mongo';
    protected $collection = 'posts';
}