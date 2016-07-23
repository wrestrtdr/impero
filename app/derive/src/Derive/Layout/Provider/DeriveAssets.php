<?php namespace Derive\Layout\Provider;

use Pckg\Framework\Provider;

class DeriveAssets extends Provider
{

    public function assets()
    {
        return [
            //'libraries' => [
            //"js/jquery-1.11.3.min.js",
            //"js/jquery-ui-1.9.2.custom.min.js",
            "js/jquery.validationEngine.js",
            "js/colorbox/jquery.colorbox.js",
            "js/maestro/func.js",
            "js/bootstrap.min.js",
            //],
            //'main'      => [
            "js/jquery.blueimp-gallery.min.js",
            "js/bootstrap-image-gallery.min.js",
            "js/default.js",
            "js/cookie.js",
            //"js/ga.php",
            "js/oldbrowser.js",
            "js/moment.js",
            "js/bootstrap-datetimepicker.min.js",
            //"vendor/eternicode/bootstrap-datepicker/js/bootstrap-datepicker.js",
            "js/hideShowPassword.min.js",

            "js/editorder.js",
            "js/narocilnica.js",
            "js/predracun.js",
            "js/profile.js",

            "css/default.css",
            "css/colorbox.css",
            "css/bootstrap.min.css",
            "css/blueimp-gallery.min.css",
            "css/bootstrap-image-gallery.min.css",
            "css/bootstrap-datetimepicker.min.css",
            //"vendor/eternicode/bootstrap-datepicker/css/datepicker.css",
            //],
        ];
    }

}