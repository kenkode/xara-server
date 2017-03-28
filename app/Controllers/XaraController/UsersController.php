<?php



/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends Controller{
    /**
    * display a list of system users
    */
    public function index(){
        $users = User::all();
        return View::make('users.index')->with('users', $users);
    }

    /**
    * display the edit page
    */
    public function edit($user){

        $user = User::find($user);

        return View::make('users.edit')->with('user', $user);
    }


     /**
    * updates the user
    */
    public function update($user){

        $user = User::find($user);

        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->update();

        return Redirect::to('users/profile/'.$user->id);
    }
    /**
     * Displays the form for account creation
     *
     * @return  Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return View::make('users.create', compact('roles'));
    }
    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());



        if ($user->id) {
            if (Config::get('confide::signup_email')) {
                Mail::queueOn(
                    Config::get('confide::email_queue'),
                    Config::get('confide::email_account_confirmation'),
                    compact('user'),
                    function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->username)
                            ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    }
                );
            }
            return Redirect::to('/');
                
        } else {
            $error = $user->errors()->all(':message');

            return Redirect::action('UsersController@create')
                ->withInput(Input::except('password'))
                ->with('error', $error);
        }
    }

    /**
     * Displays the login form
     *
     * @return  Illuminate\Http\Response
     */
    public function login()
    {
        if (Confide::user()) {
            return Redirect::to('/dashboard');

        } else {
            return View::make('login');
        }
    }

    /**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
    public function doLogin()
    {
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input)) {
            return Redirect::intended('/dashboard');
        } else {
            if ($repo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('UsersController@login')
                ->withInput(Input::except('password'))
                ->with('error', $err_msg);
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@login')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return  Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function doForgotPassword()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::action('UsersController@doForgotPassword')
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return View::make(Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@resetPassword', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        Confide::logout();

        return Redirect::to('/');
    }


    /**
    * Activate the user
    *
    */

    public function activate($user){

        $user = User::find($user);

        $user->confirmed = 1;
        $user->save();

        return Redirect::to('users');
    }


    /**
    * Deactivate the user
    *
    */

    public function deactivate($user){

        $user = User::find($user);

        $user->confirmed = 0;
        $user->save();

        return Redirect::to('users');
    }


    /**
    * Delete the user
    *
    */

    public function destroy($user){

        $user = User::find($user);

        
        $user->delete();

        return Redirect::to('users');
    }


    /**
    * change user password
    */
    public function changePassword($user){

        $user = User::find($user);

        $password_confirmation = Input::get('password_confirmation');
        $password = Input::get('password');

        if($password != $password_confirmation){

            return Redirect::to('users/password/'.$user->id)->with('error', 'passwords do not match');
        } 
        else
        {

            $user->password = Hash::make($password);
            $user->update();

            return Redirect::to('users/profile/'.$user->id);
        }



    }


    public function password($user){

        $user = User::find($user);
        return View::make('users.password', compact('user'));

    }





    public function profile($user){

        $user = User::find($user);

        return View::make('users.profile', compact('user'));
    }


    public function add(){
        $user = new User;
        $user->username = 'admin';
        $user->email = 'admin@rental.com';
        $user->password = Hash::make('password123');
        $user->confirmation_code = 'eoioweq982jwe';
        $user->remember_token = 'jsadksjd928323';
        $user->confirmed = '1';
        $user->save();
        echo "user created";
    }







    public function changePassword2(){

        $user_id = Confide::user()->id;

        

        $password_confirmation = Input::get('password_confirmation');
        $password = Input::get('password');

        if($password != $password_confirmation){

            return Redirect::back()->with('error', 'passwords do not match');
        } 
        else
        {




            
        $pass = Hash::make($password);

        DB::table('users')->where('id', $user_id)->update(array('password' => $pass));

        return Redirect::to('users/logout');



            

            
        }



    }


    public function password2(){

        $user = Confide::user()->id;
        return View::make('css.password', compact('user'));

    }


    public function tellers(){

        $tellers = DB::table('users')->where('user_type', '=', 'teller')->get();

        return View::make('tellers.index', compact('tellers'));
    }

    public function createteller($id){

        $user = User::findorfail($id);

        $user->user_type = 'teller';
        $user->is_active = true;
        $user->update();

        return Redirect::to('tellers');
    }


    public function activateteller($id){

        $user = User::findorfail($id);

        
        $user->is_active = true;
        $user->update();

        return Redirect::to('tellers');
    }


    public function deactivateteller($id){

        $user = User::findorfail($id);

        
        $user->is_active = false;
        $user->update();

        return Redirect::to('tellers');
    }


    public function credits(){
        $credits = DB::table('users')->where('user_type', '=', 'credit')->get();
        return View::make('credits.index', compact('credits'));
    }

    public function createcredit($id){
        $user = User::findorfail($id);
        $user->user_type = 'credit';
        $user->is_active = true;
        $user->update();
        return Redirect::to('credits');
    }

    public function activatecredit($id){
        $user = User::findorfail($id);
        $user->is_active = true;
        $user->update();
        return Redirect::to('credits');
    }


    public function deactivacredit($id){
        $user = User::findorfail($id);        
        $user->is_active = false;
        $user->update();
        return Redirect::to('credits');
    }




    public function newuser(){

        $input = Input::all();

        $roles = Input::get('role');

        $user = new User;

        $user->username = array_get($input, 'username');
        $user->email    = array_get($input, 'email');
        $user->password = array_get($input, 'password');
        $user->user_type = array_get($input, 'user_type');
        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $user->password_confirmation = array_get($input, 'password_confirmation');
        // Generate a random confirmation code
        $user->confirmation_code     = md5(uniqid(mt_rand(), true));
        // Save if valid. Password field will be hashed before save
        $user->save();
        foreach ($roles as $role) {
            $user->attachRole($role);
        }        
        return Redirect::to('users');
    }
}
