<?php 

namespace App\Utils;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Ramsey\Uuid\Uuid;

class ViewManager {
    private const string VIEWS_PATH = "/../../../views/";

    private static function clean(string $var) : string {
        return htmlentities($var, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    public static function render(string $view_name, array $vars) : void {        
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
        header("Content-Security-Policy: default-src 'none'; script-src 'nonce-$nonce'; style-src 'self'; font-src 'self'; connect-src 'self'; img-src 'self' cataas.com; frame-ancestors 'none'; form-action 'self'; base-uri 'none';");

        # for every variable in $vars, it replaces it with the cleaned version
        array_walk_recursive($vars, function(&$value) {
            if(is_string($value)) {
                $value = self::clean($value);
                return;
            }

            if(!is_bool($value) && !is_int($value) && !is_double($value)) {
                throw new Exception("Invalid parameter - only string, bool, int and double are accepted");
            }
        });
        
        include __DIR__ . self::VIEWS_PATH . $view_name . "_view.php";
    }
}