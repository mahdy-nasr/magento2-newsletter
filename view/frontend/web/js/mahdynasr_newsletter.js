/**
 * Created by PhpStorm.
 * User: mahdynasr
 * Date: 03/04/18
 */
//----------------------------

require(['jquery', 'jquery/ui'], function($){

    //close handler function
    $("#newsletter-close").click(function () {
        $(".block.mahdy-newsletter").first().animate({width:'toggle'},350);
    });
});