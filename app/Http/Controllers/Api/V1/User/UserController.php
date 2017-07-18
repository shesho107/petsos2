<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\PetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	protected $user_repository;
	protected $pet_repository;
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserRepository $user_repository, PetRepository $pet_repository)
	{
		$this->user_repository = $user_repository;
		$this->pet_repository = $pet_repository;
	}

	public function all()
	{
		return response()->json($this->user_repository->all(), 200);
	}

	public function create(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email'     => ['required', 'email', 'unique:users,email'],
			'password'  => ['required', 'min:6'],
			'name'      => ['required'],
			'username'  => ['required'],
			'user_type' => ['required'],
			'district'  => ['required']
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->getMessages()], 400);
		}

		$data = [
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'password' => app('hash')->make($request->get('password')),
			'user_type_id' => $request->get('user_type'),
			'district_id'=> $request->get('district'),
			'username' => $request->get('username')
		];

		$user = $this->user_repository->save($data);

		$pet_data = [
			'name' => $request->get('pet_name'),
			'age' => $request->get('pet_age'),
			'height' => $request->get('pet_height'),
			'user_id' => $user->id,
			'race_id' => $request->get('pet_race')
		];

		if ($request->file('pet_photo')) {
			$pet_path = $user->id . '_' . str_random(20) . '.png';
			$request->file('pet_photo')->move('uploads', $pet_path);
			$pet_data ['photo_path'] = 'uploads/' . $pet_path;
		}

		$pet = $this->pet_repository->save($pet_data);

		return response()->json(['success' => 'User registered'], 200);
	}

	public function getUser(Request $request)
	{
		$token_user = $request->user();

		$data = $this->user_repository->get($token_user->id);

		return response()->json(['success' => ['user' => $data['user']]], 200);
	}

	public function update(Request $request)
	{
		$user_id = $request->user()->id;

		$validator = Validator::make($request->all(), [
			'email'     => ['required', 'email', 'unique:users,email,' . $user_id],
			'password'  => ['min:6'],
			'name'      => ['required'],
			'username'  => ['required'],
			'user_type' => ['required'],
			'district'  => ['required']
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->getMessages()], 400);
		}

		$data = [
			'id' => $user_id,
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'user_type_id' => $request->get('user_type'),
			'district_id'=> $request->get('district'),
			'username' => $request->get('username')
		];

		if ($request->has('password')) {
			$data['password'] = app('hash')->make($request->get('password'));
		}

		$user = $this->user_repository->save($data);

		return response()->json(['success' => 'User updated'], 200);
	}

	public function getAnotherUser(Request $request, $id)
	{
		$data = $this->user_repository->get($id);

		return response()->json(['success' => ['user' => $data['user'], 'posts' => $data['posts']]], 200);
	}
}
