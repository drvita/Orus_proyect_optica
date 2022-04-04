<?php
function isAdmin($user)
{

    return $user->hasRole("admin");
}

function user_can($user, $can)
{
    return $user->can($can);
}

function user_hasRole($user, $role)
{
    return $user->hasRole($role);
}
