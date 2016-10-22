jQuery(document).ready(function(){
    jQuery('.ptz-btn').on("click", function(){
        var action = jQuery(this).attr('ptzmove');
        var camera = jQuery(this).parents('.camera').attr('cameraname');
        //alert("Move camera " + camera + " in direction " + action);
        jQuery.ajax({
            url: "/camera/ptz",
            method: 'post',
            data: {
                action: action,
                camera: camera,
            }
        });

    });
});