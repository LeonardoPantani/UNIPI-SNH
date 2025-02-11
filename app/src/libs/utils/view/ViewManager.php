<?php 

namespace App\Utils;

require __DIR__ . '/../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

class ViewManager {
    private const VIEWS_PATH = "/../../../views/";

    private static function clean(string $var) : string {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }

    public static function render(string $view_name, array $vars) : void {
        # for every variable in $vars, it replaces it with the cleaned version
        $vars = array_map(function($value) {
            if(is_string($value)) return self::clean($value); else return $value;
        }, $vars);

        $nonce = (Uuid::uuid4())->toString();

        header("Content-Security-Policy: default-src 'self'; script-src 'nonce-$nonce'; img-src 'self' cataas.com");

        include __DIR__ . self::VIEWS_PATH . $view_name . "_view.php";
    }
}