$(document).ready(function () {
    const email = localStorage.getItem("userEmail");
    const sessionId = localStorage.getItem("sessionId");

    if (!email || !sessionId) {
        window.location.href = 'login.html';
        return;
    }

    $.ajax({
        url: 'php/profile/get_profile.php',
        type: 'POST',
        data: { email: email, sessionId: sessionId },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.error) {
                localStorage.clear();
                window.location.href = 'login.html';
                return;
            }
            $('#username').val(data.username);
            $('#email').val(data.email);
            $('#age').val(data.age);
            $('#dob').val(data.dob);
            $('#contact').val(data.contact);
        },
        error: function () {
            $('#msg').html('<div class="alert alert-danger">Failed to load profile</div>');
        }
    });

    $('#profileForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'php/profile/update_profile.php',
            type: 'POST',
            data: {
                email: email, // Original email for identification
                new_email: $('#email').val(), // New email to update to
                sessionId: sessionId,
                username: $('#username').val(),
                age: $('#age').val(),
                dob: $('#dob').val(),
                contact: $('#contact').val()
            },
            success: function (response) {
                let res = response;
                if (typeof response === 'string') {
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        console.error("Could not parse JSON", e);
                        $('#msg').html('<div class="alert alert-danger">Server returned invalid format</div>');
                        return;
                    }
                }

                if (res.success) {
                    if (res.new_email) {
                        localStorage.setItem("userEmail", res.new_email);
                        $('#msg').html('<div class="alert alert-success">' + res.message + ' Reloading...</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        $('#msg').html('<div class="alert alert-success">' + res.message + '</div>');
                    }
                } else {
                    $('#msg').html('<div class="alert alert-danger">' + (res.error || 'Update failed') + '</div>');
                }
            },
            error: function (xhr) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    $('#msg').html('<div class="alert alert-danger">' + (res.error || 'Update failed') + '</div>');
                } catch (e) {
                    $('#msg').html('<div class="alert alert-danger">Failed to update profile</div>');
                }
            }
        })
    })
});