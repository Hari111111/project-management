<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('currentUserRole')) {
    function currentUserRole() {
        return Auth::check() ? Auth::user()->getRoleNames()->first() : null;
    }
}