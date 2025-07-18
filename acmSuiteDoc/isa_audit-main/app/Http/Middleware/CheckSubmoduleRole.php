<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\ModulesModel;
use App\Models\Admin\SubModulesModel;
use App\Models\Admin\ProfilesPermissionModel;
use App\User;

class CheckSubmoduleRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        // Get submodule by path
        $ismodule = true;
        $path = $currentPath = Route::getFacadeRoot()->current()->uri();
        $splitString = explode('/', $path);
        $submodule = SubModulesModel::GetSubModulePath($path);
        if(is_object($submodule) || !isset($submodule['id_submodule'])){
            return response()->view('errors.404');
        }
        $userInfo = User::getUserInfo(Auth::id());
        // Reset modules menu
        // Getting the modules and submodules allowed for profile
        $modulesAllowed = ProfilesPermissionModel::GetViewPermissionByProfile($userInfo[0]['id_profile']);
        // Getting module information
        $activeModules = ModulesModel::GetModulesByIds($modulesAllowed);
        foreach($activeModules as $index => $mod) {
            $activeModules[$index]['submodules'] = [];
            $activeModules[$index]['collapse'] = is_null($mod['path']) && $splitString[0] == 'catalogs';
            $activeModules[$index]['active'] = $request->is($mod['path']) || is_null($mod['path']);
            $activeModules[$index]['submodules'] = SubModulesModel::GetGroupedSubModules($userInfo[0]['id_profile'], $mod['id_module']);
            foreach ($activeModules[$index]['submodules'] as $key => $s) {
                if($mod['has_submodule']) {
                    $activeModules[$index]['submodules'][$key]['active'] = $request->is($s['path']);
                }
            }
        }
        Session::put('menu', $activeModules);
        // Check permission in submodule
        $permission = ProfilesPermissionModel::GetSubmodulePermission($submodule['id_submodule'], $userInfo[0]['id_profile']);
        if (is_object($permission) || (is_array($permission) && !isset($permission[0])) || $permission[0]['visualize'] != 1) {
            // Redirect to view Unauthorized
            return response()->view('errors.401');
        }else{
            // Set permission in session
            Session::put('create', (isset($permission[0]['create']) && $permission[0]['create'] == 1) ? 1 : 0);
            Session::put('modify', (isset($permission[0]['modify']) && $permission[0]['modify'] == 1) ? 1 : 0);
            Session::put('delete', (isset($permission[0]['delete']) && $permission[0]['delete'] == 1) ? 1 : 0);
        }    
        return $next($request);
    }
}
