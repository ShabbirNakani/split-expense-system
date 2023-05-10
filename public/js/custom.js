$(function () {

    //for image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview').hide();
                $('#image-preview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    // for image preview
    $("#file-input").change(function () {
        readURL(this);
    });

    //notification
    setTimeout(function () {
        // Closing the alert
        $('.alert').hide('slow');
    }, 3000);

    //select 2 option menu
    $('.js-example-basic-multiple').select2();

    // js for profile picture showing after validation
    // const profilePic = $("#profile-pic-register");
    // const profilePicPreview = $('#image-preview');
    // image preview for register view
    $("#profile-pic-register").change(function () {
        readURL(this);
        // console.log(profilePic[0].files[0]);
        // const url = URL.createObjectURL(profilePic[0].files[0]);
        // console.log(url);
        // profilePicPreview.attr('src', url);
    });


})



