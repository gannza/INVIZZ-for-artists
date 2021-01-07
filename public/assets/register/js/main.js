var step = 0;
$(function(){
    $("#form-register").validate({
        rules: {
            password : {
                required : true,
            },
            confirm_password: {
                equalTo: "#password"
            }
        },
        messages: {
            username: {
                required: "Please provide an username"
            },
            email: {
                required: "Please provide an email"
            },
            password: {
                required: "Please provide a password"
            },
            confirm_password: {
                required: "Please provide a password",
                equalTo: "Please enter the same password"
            }
        }
    });
    $("#form-total").steps({
        headerTag: "h2",
        bodyTag: "section",
        transitionEffect: "none",
        // enableAllSteps: true,
        autoFocus: true,
        transitionEffectSpeed: 500,
        titleTemplate : '<div class="title">#title#</div>',
        labels: {
            previous : 'Back',
            next : '<i class="zmdi zmdi-arrow-right"></i>',
            finish : '<i class="zmdi zmdi-arrow-right"></i>',
            current : ''
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            var username = $('#username').val();
            var email = $('#email').val();
            var jobtype = $('#job_type').val();
            var phonenumber = $('#phone').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var zipcode = $('#zipcode').val();
            var imgsrc = $('.your_picture_image').attr('src');

            $('#username-val').text(username);
            $('#email-val').text(email);
            $('#job-type-val').text(jobtype);
            $('#phone-number-val').text(phonenumber);
            $('#state-val').text(state);
            $('#city-val').text(city);
            $('#zipcode-val').text(zipcode);
            // $('#imgsrc-val').text(imgsrc);

            $("#form-register").validate().settings.ignore = ":disabled,:hidden";
            return $("#form-register").valid();
        }
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.your_picture_image')
                .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }

}
