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

        /*
            links: https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
                   https://web.dev/articles/strict-csp
                   https://csp-evaluator.withgoogle.com
                   https://content-security-policy.com/

            default-src 'none' => all *-src are disabled (there's no need to use "media-src 'none'", "object-src 'none'", etc.).
                                  It does not allow any resources to load

            script-src 'nonce-' => A CSP based on nonces or hashes is called 'strict CSP'

            media-src 'none'  =>  <audio> and <video> tags are not allowed since it is possible 
                                  to exploit them in order to perform XSS (e.g. with 'onerror' attribute).
                                  However, from mozilla (https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP):
                                    "If a CSP contains either a default-src or a script-src 
                                    directive, then inline JavaScript will not be allowed to 
                                    execute unless extra measures are taken to enable it.
                                    The 'unsafe-inline' keyword can be used to override this 
                                    restriction"
                                  Therefore, inline javascript is disabled and so there is no way to exploit a 
                                  media tag, even if they are allowed

            frame-ancestors 'none' =>  From mozilla (https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP#clickjacking_protection):
                                        It allows to control which documents, if any, are allowed to embed this 
                                        document in a nested browsing context such as an <iframe>
                                       It allows to prevent framing attacks like clickjacking

            base-uri 'none' => block the injection of <base> tags. This prevents attackers from changing 
                               the locations of scripts loaded from relative URLs
        */
        header("Content-Security-Policy: default-src 'none'; script-src 'nonce-$nonce'; style-src 'self'; font-src 'self'; img-src 'self' cataas.com; frame-ancestors 'none'; base-uri 'none';");

        include __DIR__ . self::VIEWS_PATH . $view_name . "_view.php";
    }
}