<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $table = 'posts';

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id');
	}

	public function post_type()
	{
		return $this->belongsTo('App\Models\PostType', 'post_type_id');
	}

	public function pet()
	{
		return $this->belongsTo('App\Models\Pet', 'pet_id');
	}

	public function comments()
	{
		return $this->hasMany('App\Models\Comment', 'post_id');
	}
}
