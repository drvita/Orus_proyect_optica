<?php
function isAdmin($user)
{

    return $user->role("admin");
}

function user_can($user, $can)
{
    return $user->can($can);
}

function user_hasRole($user, $role)
{
    return $user->role($role);
}
