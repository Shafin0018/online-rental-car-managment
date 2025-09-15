$(document).ready(function() {
    $("#loginForm").submit(function(e) {
        let email = $("#email").val().trim();
        let password = $("#password").val().trim();
        let valid = true;
        if (email === "") {
            document.getElementById("emailError").innerHTML = "Email is required";
            valid = false;
        } else {
            document.getElementById("emailError").innerHTML = "";
        }
        if (password === "") {
            document.getElementById("passwordError").innerHTML = "Password is required";
            valid = false;
        } else {
            document.getElementById("passwordError").innerHTML = "";
        }
        if (!valid) e.preventDefault();
    });

    $("#registerForm").submit(function(e) {
        let name = $("#name").val().trim();
        let email = $("#regEmail").val().trim();
        let password = $("#regPassword").val().trim();
        let role = $("#role").val();
        let valid = true;
        if (name === "") {
            document.getElementById("nameError").innerHTML = "Name required";
            valid = false;
        } else {
            document.getElementById("nameError").innerHTML = "";
        }
        if (email === "" || !email.includes("@")) {
            document.getElementById("regEmailError").innerHTML = "Valid email required";
            valid = false;
        } else {
            document.getElementById("regEmailError").innerHTML = "";
        }
        if (password.length < 6) {
            document.getElementById("regPasswordError").innerHTML = "Min 6 characters";
            valid = false;
        } else {
            document.getElementById("regPasswordError").innerHTML = "";
        }
        if (role === "") {
            document.getElementById("roleError").innerHTML = "Select role";
            valid = false;
        } else {
            document.getElementById("roleError").innerHTML = "";
        }
        if (!valid) e.preventDefault();
    });

    $("#otpForm").submit(function(e) {
        let otp = $("#otp").val().trim();
        if (otp.length !== 6 || isNaN(otp)) {
            document.getElementById("otpError").innerHTML = "Enter 6 digit OTP";
            e.preventDefault();
        } else {
            document.getElementById("otpError").innerHTML = "";
        }
    });

    $(".validateForm").submit(function(e) {
        let valid = true;
        $(this).find("input, select").each(function() {
            if ($(this).val().trim() === "") {
                let errorId = $(this).attr("id") + "Error";
                document.getElementById(errorId).innerHTML = "Required";
                valid = false;
            } else {
                let errorId = $(this).attr("id") + "Error";
                document.getElementById(errorId).innerHTML = "";
            }
        });
        if (!valid) e.preventDefault();
    });
});

