<canvas id="takePhotoCanvas" width="320" height="240" class="pull-right img-circle" ></canvas>
<video id="takePhotoVideo" width="320" height="240" autoplay ></video>
<script type="text/javascript">
    // Put event listeners into place

    

    var canvas = document.getElementById("takePhotoCanvas"),
        // context = canvas.getContext("2d"),
        video = document.getElementById("takePhotoVideo"),
        videoObj = { "video": true },
        errBack = function(error) {
            console.log("Video capture error: ", error.code); 
        };

    // Put video listeners into place
    if(navigator.getUserMedia) { // Standard
        navigator.getUserMedia(videoObj, function(stream) {
            video.src = stream;
            video.play();
            takePhoto = stream;
        }, errBack);
    } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
        navigator.webkitGetUserMedia(videoObj, function(stream){
            video.src = window.webkitURL.createObjectURL(stream);
            video.play();
            takePhoto = stream;
        }, errBack);
    }
    else if(navigator.mozGetUserMedia) { // Firefox-prefixed
        navigator.mozGetUserMedia(videoObj, function(stream){
            video.src = window.URL.createObjectURL(stream);
            video.play();
            takePhoto = stream;
        }, errBack);
    }

    // Trigger photo take
    document.getElementById("takePhotoSnap").addEventListener("click", function() {
        var v = $('#takePhotoVideo'), c = $('#takePhotoCanvas');

        // $('#takePhotoCanvas').width($('#takePhotoVideo').width());
        // $('#takePhotoCanvas').height($('#takePhotoVideo').height());

        context = c[0].getContext("2d");

        var w = c.width(), h = c.height();
        
        context.drawImage(video, 0, 0, 640,480, 0,0, 320,240);

        $('#takePhotoSave').removeClass('disabled');
        $('#takePhotoSave').addClass('input-focused');
    });

    function takePhotoSave (img) {
        var img = convertCanvasToImage(img[0]);
        $.ajax({
            url      : '/{$Xtra}/{$method}/takePhoto/.json',
            type     : "POST",
            data     : {
                src : img.src
            },
            dataType : 'json',
            success  : function(data, textStatus, jqXHR){
                $('.profile-pic').attr({
                    src : img.src
                });

                $('.profile-pic img').attr({
                    src : img.src
                });
            }
        });
    }

    function convertCanvasToImage(canvas) {
        var image = new Image();
        image.src = canvas.toDataURL("image/png");
        IMG = image;
        return image;
    }
     
</script>