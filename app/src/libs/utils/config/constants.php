<?php
    // PATHS
    const ROOT_PATH             = '/';                    // /storyforge
    const REGISTRATION_PATH     = '/registration';        // /storyforge/registration.php
    const LOGIN_PATH            = '/login';               // /storyforge/login.php
    const LOGOUT_PATH           = '/logout';              // /storyforge/logout.php
    const FORGOT_PASSWORD_PATH  = '/password/forgot';     // /storyforge/forgot_password.php
    const RESET_PASSWORD_PATH   = '/password/reset';      // /storyforge/create_password.php
    const ADD_NOVEL_PATH        = '/novel/add';           // /storyforge/add_novel.php
    const SETTINGS_PATH         = '/user/settings';       // /storyforge/settings.php
    const SHOW_NOVELS_PATH      = '/novels';
    const SHOW_USER_NOVELS_PATH = '/user/novels';
    
    const ADMIN_PATH            = '/admin';               // /storyforge/admin/panel.php
    const ADMIN_EDIT_USER_PATH  = '/admin/services/edit'; // /storyforge/admin/edit_user_service.php
    
    const API_V1_PATH           = '/api/v1';
    const API_SEARCH_USERS_PATH = API_V1_PATH . '/users';


    // DYNAMIC PATHS
    function show_novel_path($uuid): string
    {
        return SHOW_NOVELS_PATH . "/$uuid";
    }

    function create_password_path($code): string
    {
        return RESET_PASSWORD_PATH . "/$code";
    }