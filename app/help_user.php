<?php
if (!function_exists('isAdmin')) {
    function isAdmin($user)
    {

        return $user->role("admin");
    }
}

if (!function_exists('user_can')) {
    function user_can($user, $can)
    {
        return $user->can($can);
    }
}

if (!function_exists('user_hasRole')) {
    function user_hasRole($user, $role)
    {
        return $user->role($role);
    }
}
