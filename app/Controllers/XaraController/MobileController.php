<?php

class MobileController extends \BaseController {
	/**
	 * Display a listing of loanaccounts
	 *
	 * @return Response
	 */
	public function loginAttempt()
	{		
		$user=Input::all();
		$username=array_get($user,'username');
		$password=array_get($user,'password');
		$data=array(
				'username'=>$username,
				'password'=>Hash::make($password)
			);
		$creds=array();
		$user=User::where($data)->first();
		if(count($user)>0){
			$creds['success']=1;
			$creds['user_id']=$user->id;
		}else{
			$creds['fail']=0;
		}
		echo json_encode($creds);
	}






}