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

    jQuery(document).keydown(function(e) {
        var action = "";
        switch(e.which) {
            case 65: // a
                action = "left";
                break;

            case 83: // s
                action = "down";
                break;

            case 68: // d
                action = "right";
                break;

            case 87: // w
                action = "up";
                break;

            default: return; // exit this handler for other keys
        }

        jQuery('input[type=checkbox].wasd-checkbox').each(function () {
            if(this.checked){
                var camera = jQuery(this).parents('.camera').attr('cameraname');

                jQuery.ajax({
                    url: "/camera/ptz",
                    method: 'post',
                    data: {
                        action: action,
                        camera: camera
                    }
                });

            }
        });
    });
});