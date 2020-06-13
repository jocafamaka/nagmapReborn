<?php

function jsonResponse($data, $code = 200)
{
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    die();
}

function requiredAuth($useAuth, $user, $userPass, $L)
{
    if ($useAuth == 1) {
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $is_not_authenticated = (empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER'] != $user ||
            $_SERVER['PHP_AUTH_PW']   != $userPass);
        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            die('{"error": "' . $L::accessDenied . '"}');
        }
    }
}

function checkDefaultAuth($useAuth, $user, $userPass)
{
    return (($useAuth == 1) ? ($user == "ngradmin" && $userPass == "ngradmin") : false);
}
