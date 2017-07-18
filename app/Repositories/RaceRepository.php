<?php

namespace App\Repositories;

use DB;
use App\Models\Race;

class RaceRepository
{
	protected $model;

	public function __construct()
	{
		$this->model = new Race;
	}

	public function all()
	{
		return $this->model->all()->transform(function($item, $key){
			$obj = $item;
			$obj->animal = $item->animal;
			return $obj;
		});
	}

	public function getRaces($filter = null)
	{
		return $filter ? $this->model->where('animal_id', $filter)->get() : $this->all();
	}
}