<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\QueryException as Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Classes\StatusConstants;

class User extends Authenticatable
{
    use Notifiable;
    /** 
     *  Table specification in model
    */
    protected $table = 't_users';
    protected $primaryKey = 'id_user';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'person',
    ];

    /**
     * The relation that allow use in api request.
     *
     * @var array
     */
    protected $allow_included = [
        'person',
    ];

     /**
     * The relation that allow use in api request.
     *
     * @var array
     */
    protected $allow_filter = [
        'id_customer' => ['field' => 'id_customer', 'type' => 'number', 'relation' => null],
        'id_corporate' => ['field' => 'id_corporate', 'type' => 'number', 'relation' => null],
    ];

    public function person() {
        return $this->belongsTo('App\Models\Admin\PeopleModel', 'id_person', 'id_person');
    }
    public function profile() {
        return $this->belongsTo('App\Models\Admin\ProfilesModel', 'id_profile', 'id_profile');
    }
    /**
     * Return user info
     */
    public function scopeGetUserInfo($query, $idUser) 
    {
        $query->join('t_profiles', 't_profiles.id_profile', 't_users.id_profile')
            ->join('t_people', 't_people.id_person', 't_users.id_person')
            ->join('t_corporates', 't_corporates.id_corporate', 't_users.id_corporate')
            ->join('t_profile_types', 't_profile_types.id_profile_type', 't_profiles.id_profile_type')
            ->select('t_users.id_user', 't_users.id_profile', 't_users.email', 't_users.secondary_email',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'),
                \DB::raw('DATE_FORMAT(t_people.birthdate, "%Y-%m-%d") AS birthdate'), 't_users.id_customer',
                't_people.first_name', 't_people.second_name', 't_people.last_name', 't_people.rfc', 't_people.phone', 't_people.gender',
                't_users.picture', 't_users.id_status', 't_users.id_person', 't_profiles.profile_name', 't_users.id_profile',
                't_users.id_corporate', 't_corporates.corp_tradename', 't_profiles.id_profile_type', 't_profile_types.profile_level')
            ->where('t_users.id_user', $idUser);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Return user info by its email
     */
    public function scopeGetUserInfoByEmail($query, $email){
        $user = User::select('id_user')->where('email', $email)->orWhere('secondary_email', $email)->limit(1)->get()->toArray();
        if( is_array($user) && isset($user[0]) && isset($user[0]['id_user']) ){
            return User::getUserInfo($user[0]['id_user']);            
        }else{
            return array();
        }
    }
    /**
     * Save reset token
     */
    public function scopeSetResetToken($query, $idUser, $token){
        $model = User::findOrFail($idUser);
        $model->remember_token = $token;
        $model->save();
    }
    /**
     * Return user info by its token
     */
    public function scopeGetUserInfoByToken($query, $token){
        $user = User::select('id_user')->where('remember_token', '=', $token)->limit(1)->get()->toArray();
        if( is_array($user) && isset($user[0]) && isset($user[0]['id_user']) ){
            return User::getUserInfo($user[0]['id_user']);            
        }else{
            return array();
        }
    }
    /**
     * Set and encrypt new password
     */
    public function scopeUpdatePassword($query, $password, $idUser){
        $model = User::findOrFail($idUser);
        $model->password = bcrypt($password);
        $model->save();
        User::deleteResetToken($model->id_user);
    }
    /**
     * Delete used token
     */
    public function scopeDeleteResetToken($query, $idUser){
        $model = User::findOrFail($idUser);
        $model->remember_token = null;
        $model->save();
    }
    /**
     * Return user info
     */
    public function scopeGetUsersDT($query, $page, $rows, $search, $draw, $order, $fIdCustomer, $fIdCorporate){
        $query->join('t_profiles', 't_profiles.id_profile', 't_users.id_profile')
            ->join('t_profile_types', 't_profiles.id_profile_type', 't_profile_types.id_profile_type')
            ->join('t_people', 't_people.id_person', 't_users.id_person')
            ->join('c_status', 'c_status.id_status', 't_users.id_status')
            ->select('t_users.id_user', 't_users.id_profile', 't_users.email', 't_users.secondary_email',
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'),
                \DB::raw('CONCAT(t_people.second_name,\' \',t_people.last_name) as last_names'),
                \DB::raw('DATE_FORMAT(t_people.birthdate, "%d/%m/%Y") AS birthdate'), 't_profile_types.type',
                't_people.first_name', 't_people.second_name', 't_people.last_name', 't_people.rfc', 't_people.phone', 't_people.gender',
                't_users.picture', 't_users.id_status', 'c_status.status', 't_profiles.profile_name', 't_users.id_corporate');
        // Add filters
        // if ($fContract != null) {
        //     $query->where('t_contracts.contract','LIKE','%'.$fContract.'%');
        // }
        // if ($fIdStatus != 0) {
        //     $query->where('t_contracts.id_status', $fIdStatus);
        // }
        if ($fIdCustomer != 0) {
            $query->where('t_users.id_customer', $fIdCustomer);
        }
        if ($fIdCorporate != 0) {
            $query->where('t_users.id_corporate', $fIdCorporate);
        }
        //Order by
        switch ($order[0]['column']) {
            case 1:
                $columnSwitch = 't_people.first_name';
                break;
            case 2:
                $columnSwitch = 'last_names';
                break;
            case 3:
                $columnSwitch = 't_users.email';
                break;
            case 4:
                $columnSwitch = 't_people.phone';
                break;
            case 5:
                $columnSwitch = 't_profiles.profile_name';
                break;
            case 6:
                $columnSwitch = 'c_status.status';
                break;
            default:
                $columnSwitch = 't_users.id_user';
                break;
        }
        $column = $columnSwitch;   
        $dir = (isset($order[0]['dir']) ? $order[0]['dir'] : 'desc');            
        $query->orderBy($column, $dir);
        
        /*Search*/
        $query->where(function($query) use ($search){
            $query->where('t_users.email','LIKE','%'.$search['value'].'%'); 
        });

        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
    /**
    * Get users by corporation 
    */
    public function scopeGetUsers($query, $idCorporate){
        $query->join('t_people', 't_people.id_person', 't_users.id_person')
        ->select(
            't_users.id_user', 
            't_users.picture', 
            \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name')
        );
        $where = []; 
        if ($idCorporate) array_push($where, ['t_users.id_corporate', $idCorporate]);
        if($where) $query->where($where);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Set data user in register
     */
    public function scopeSetUser($query, $idPerson, $password, $data){
        $model = new User;
        $model->email = $data['email'];
        $model->password = bcrypt($password);
        $model->id_customer = $data['idCustomer'];
        $model->id_corporate = $data['idCorporate'];
        $model->id_person = $idPerson;
        $model->id_status = $data['idStatus'];
        $model->id_profile = $data['idProfile'];
        try {
            $model->save();
            $dataResponse['status'] = StatusConstants::SUCCESS;
            $dataResponse['idUser'] = $model->id_user;
            return $dataResponse;
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                $dataResponse['status'] = StatusConstants::DUPLICATE;
                return $dataResponse;
            }else{
                $dataResponse['status'] = StatusConstants::ERROR;
                return $dataResponse;
            } 
        }
    }
    /**
     * Update data user in register
     */
    public function scopeUpdateUser($query, $data){
        try {
            $model = User::findOrFail($data['idUser']);
            if ($model->email != $data['email']) {
                $model->email = $data['email'];
            }
            $model->id_customer = $data['idCustomer'];
            $model->id_corporate = $data['idCorporate'];
            $model->id_status = $data['idStatus'];
            $model->id_profile = $data['idProfile'];
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1062) {
                    return StatusConstants::DUPLICATE;
                }else{
                    return StatusConstants::WARNING;
                }
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete user
     */
    public function scopeDeleteUser($query, $idUser){
        try {
            $model = User::findOrFail($idUser);
            try {
                $model->delete();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                return StatusConstants::WARNING;  //on cascade exception
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Delete user
     */
    public function scopeSetPassword($query, $data){
        try {
            $model = User::findOrFail($data['idUser']);
            $model->password = bcrypt($data['newPassword']);
            try {
                $model->save();
                $data['model'] = $model;
                $data['status'] = StatusConstants::SUCCESS;
                return $data;
            } catch (Exception $e) {
                $data['status'] = StatusConstants::WARNING;  //on cascade exception
                return $data;
            }
        } catch (ModelNotFoundException $th) {
            $data['status'] = StatusConstants::ERROR;
            return $data;
        }
    }
    /**
     * Update data user in account
     */
    public function scopeSetUserAccount($query, $data){
        try {
            $model = User::findOrFail($data['idUser']);
            if ($model->email != $data['email']) {
                $model->email = $data['email'];
            }
            if ($model->secondary_email != $data['secondaryEmail']) {
                $model->secondary_email = $data['secondaryEmail'];
            }
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1062) {
                    return StatusConstants::DUPLICATE;
                }else{
                    return StatusConstants::ERROR;
                }
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Update user image in account
     */
    public function scopeUpdateProfileImage($query, $user, $name){
        try {
            $model = User::findOrFail($user[0]['id_user']);
            if ($model->picture != $name) {
                $model->picture = $name;
            }
            try {
                $model->save();
                return StatusConstants::SUCCESS;
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1062) {
                    return StatusConstants::DUPLICATE;
                }else{
                    return StatusConstants::ERROR;
                }
            }
        } catch (ModelNotFoundException $th) {
            return StatusConstants::ERROR;
        }
    }
    /**
     * Get users count by profile id
     */
    public function scopeGetUsersCountByProfileID($query, $idProfile)
    {
        return $query
            ->select('id_user')
            ->where('id_profile', $idProfile)
            ->count();
    }
    /**
     * Get users by corporates array
     */
    public function scopeGetUsersByCorporates($query, $idCorporatesArray){
        $query->select('id_user')
            ->where('id_status', StatusConstants::ACTIVE)
            ->whereIn('id_corporate', $idCorporatesArray);
        $data = $query->get()->toArray();
        return $data;
    }
    /**
     * Get User by corporate
     */
    public function scopeGetUserProcesss($query, $idCorporate){
        $query->join('t_people', 't_people.id_person', 't_users.id_person')
            ->select('t_users.id_user', 't_users.picture', 
                \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name'))
            ->where('t_users.id_corporate', $idCorporate);
        $data = $query->get()->toArray();
        return $data;
    }
    public function scopeGetAllUsers($query, $idLider){
        $query->join('t_people', 't_people.id_person', 't_users.id_person')
        ->select(
            't_users.id_user', 
            't_users.picture', 
            \DB::raw('CONCAT(t_people.first_name,\' \',t_people.second_name) as complete_name')
        );
        $where = []; 
        if ($idLider) array_push($where, ['t_users.id_user','DISTINCT', $idLider]);
        //if ($idLider) array_push($where, ['t_users.id_customer', $idLider]);
        if($where) $query->where($where);
        $data = $query->get()->toArray();
        return $data;
    }
}
