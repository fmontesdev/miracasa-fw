function login() {
    if (validate_login() != 0) {
        var userForm = document.getElementById('username_log').value;
        var pswdForm = document.getElementById('passwd_log').value;

        ajaxPromise(friendlyURL('?module=login'), 'POST', 'JSON', { 'op': 'login', 'username': userForm, 'password': pswdForm })
            .then(function(data) {
                console.log(data);
                // return;

                if (data == 'error_user') {
                    document.getElementById('error_username_log').innerHTML = "El usuario no existe, asegúrese de haberlo escrito corréctamente"
                } else if (data == 'error_passwd') {
                    document.getElementById('error_passwd_log').innerHTML = "La contraseña es incorrecta"
                } else if (data == 'user_inactive') {
                    document.getElementById('error_passwd_log').innerHTML = "Tu cuenta ha sido deshabilitada. Contacta con el administrador"
                } else if (data.msg == 'otp') {
                    $('#login_container').hide();
                    $('#register_container').hide();
                    $('#recoverEmail_container').hide();
                    $('#otp_container').show();
                    key_otp(data.uid);
                    button_otp(data.uid);
                } else {
                    localStorage.setItem("access_token", data.access);
                    localStorage.setItem("refresh_token", data.refresh);

                    //SweetAlert2
                    Swal.fire({
                        // position: "top-end",
                        icon: "success",
                        title: "Sesión iniciada",
                        // text: "Your work has been saved",
                        showConfirmButton: false,
                        // timer: 1500
                    });

                    var location = localStorage.getItem("location");
                    if (location == 'home')  {
                        setTimeout(function(){window.location.href = friendlyURL('?module=home');}, 1500); // redirigimos al home
                    } else if (location == 'shop') {
                        setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                    } else {
                        setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                    }
                }
            }).catch(function(textStatus) {
                if (console && console.log) {
                    console.log("La solicitud ha fallado: " + textStatus);
                }
            });
    }
}

function key_login() {
    $("#login").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            e.preventDefault();
            login();
        }
    });
}

function button_login() {
    $('#login').on('click', function(e) {
        e.preventDefault();
        login();
    });
}

function validate_login() {
    var error = false;

    if (document.getElementById('username_log').value.length === 0) {
        document.getElementById('error_username_log').innerHTML = "Tienes que introducir el usuario";
        error = true;
    } else {
        if (document.getElementById('username_log').value.length < 5) {
            document.getElementById('error_username_log').innerHTML = "El usuario tiene que tener 5 caracteres como mínimo";
            error = true;
        } else {
            document.getElementById('error_username_log').innerHTML = "";
        }
    }

    if (document.getElementById('passwd_log').value.length === 0) {
        document.getElementById('error_passwd_log').innerHTML = "Tienes que introducir la contraseña";
        error = true;
    } else {
        if (document.getElementById('passwd_log').value.length < 8) {
            document.getElementById('error_passwd_log').innerHTML = "La contraseña tiene que tener 8 caracteres como mínimo";
            error = true;
        } else {
            document.getElementById('error_passwd_log').innerHTML = "";
        }
    }

    if (error == true) {
        return 0;
    }
}

function key_otp(uid) {
    $("#login_otp").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            e.preventDefault();
            otp_login(uid);
        }
    });
}

function button_otp(uid) {
    $('#login_otp').on('click', function(e) {
        e.preventDefault();
        otp_login(uid);
    });
}

function otp_login(uid) {
    var otp = document.getElementById('otp_login').value;

    ajaxPromise(friendlyURL('?module=login'), 'POST', 'JSON', { 'op': 'otp_login', 'uid': uid, 'otp': otp })
        .then(function(data) {
            console.log(data);
            // return;

            if (data.msg == 'otp_attempts') {
                otp_attempts = 2 - data.otp_attempts;
                document.getElementById('error_otp_login').innerHTML = `El código introducido no es válido.<br> Te quedan ${otp_attempts} intentos.`
            } else if (data == 'unauthenticated') {
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');

                //SweetAlert2
                Swal.fire({
                    // position: "top-end",
                    icon: "error",
                    title: "Cuenta deshabilitada",
                    text: "Contacta con el administrador.",
                    showConfirmButton: false,
                    // timer: 1250
                  });
                  
                  setTimeout(function(){window.location.href = friendlyURL('?module=home');}, 2500); // redirigimos al home
            } else if (data.msg == 'expired_token') {
                //SweetAlert2
                Swal.fire({
                    // position: "top-end",
                    icon: "error",
                    title: "Autenticación expirada",
                    text: "Le hemos enviado otro código de autenticación",
                    showConfirmButton: false,
                    timer: 2500
                  });
            } else {
                localStorage.setItem("access_token", data.access);
                localStorage.setItem("refresh_token", data.refresh);

                //SweetAlert2
                Swal.fire({
                    // position: "top-end",
                    icon: "success",
                    title: "Sesión iniciada",
                    // text: "Your work has been saved",
                    showConfirmButton: false,
                    // timer: 1500
                });

                var location = localStorage.getItem("location");
                if (location == 'home')  {
                    setTimeout(function(){window.location.href = friendlyURL('?module=home');}, 1500); // redirigimos al home
                } else if (location == 'shop') {
                    setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                } else {
                    setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                }
            }
        }).catch(function(textStatus) {
            if (console && console.log) {
                console.log("La solicitud ha fallado: " + textStatus);
            }
        });
}

function button_socialLogin() {
    $('#google').on('click', function(e) {
        social_login('google');
    });

    $('#github').on('click', function(e) {
        social_login('github');
    });
}

function social_login(param){
    authService = firebase_config();
    authService.signInWithPopup(provider_config(param))
        .then(function(result) {
            email_name = result.user._delegate.email;
            let username = email_name.split('@');
            console.log('Autenticado usuario', username[0], result.credential.providerId);
            // console.log([result.user._delegate.uid, username[0], result.user._delegate.email, result.user._delegate.phoneNumber, result.user._delegate.photoURL, result.credential.providerId]);
            // return;
            
            if (result) {
                ajaxPromise(friendlyURL("?module=login"), 'POST', 'JSON', { 'op': 'social_login', 'uid': result.user._delegate.uid, 'username': username[0], 'email': result.user._delegate.email, 'phone': result.user._delegate.phoneNumber, 'avatar': result.user._delegate.photoURL, 'provider': result.credential.providerId })
                    .then(function(data) {
                        console.log(data);
                        // return;
                        
                        localStorage.setItem("access_token", data.access);
                        localStorage.setItem("refresh_token", data.refresh);

                        //SweetAlert2
                        Swal.fire({
                            // position: "top-end",
                            icon: "success",
                            title: "Sesión iniciada",
                            // text: "Your work has been saved",
                            showConfirmButton: false,
                            // timer: 1500
                        });

                        var location = localStorage.getItem("location");
                        if (location == 'home')  {
                            setTimeout(function(){window.location.href = friendlyURL('?module=home');}, 1500); // redirigimos al home
                        } else if (location == 'shop') {
                            setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                        } else {
                            setTimeout(function(){window.location.href = friendlyURL('?module=shop');}, 1500); // redirigimos al shop
                        }
                    })
                    .catch(function() {
                        console.log('Error: Social login error');
                    });
            }
        })
        .catch(function(error) {
            var errorCode = error.code;
            console.log(errorCode);
            var errorMessage = error.message;
            console.log(errorMessage);
            var email = error.email;
            console.log(email);
            var credential = error.credential;
            console.log(credential);
        });
}

function firebase_config(){
    if(!firebase.apps.length){
        firebase.initializeApp(FIREBASE_CONFIG);
    }else{
        firebase.app();
    }
    return authService = firebase.auth();
}

function provider_config(param){
    if(param === 'google'){
        var provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        return provider;
    }else if(param === 'github'){
        return provider = new firebase.auth.GithubAuthProvider();
    }
}

// pinta el formulario de registro desde el formulario del login
function button_toRegister() {
    $('#to_register').on('click', function() {
        $('#login_container').hide();
        $('#register_container').show();
        $('#recoverEmail_container').hide();
        $('#otp_container').hide();
    });
}

$(document).ready(function() {
    key_login();
    button_login();
    button_socialLogin();
    button_toRegister();
});