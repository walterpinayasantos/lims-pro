/**
 * Auth Module - LIMS
 */
const AuthModule = (() => {
    const ui = {
        form: "#auth_login_form",
        btnSubmit: "#auth_btn_login",
        userInput: "#auth_username",
        passInput: "#auth_password",
    };

    const init = () => {
        $(ui.form).on("submit", function (e) {
            e.preventDefault();
            handleLogin();
        });
    };

    const handleLogin = () => {
        const data = $(ui.form).serialize();

        $.ajax({
            url: window.BASE_URL + "auth/login",
            method: "POST",
            data: data,
            dataType: "json",
            beforeSend: () => {
                $(ui.btnSubmit).prop("disabled", true).html("Cargando...");
            },
            success: (res) => {
                if (res.success) {
                    window.location.href = window.BASE_URL + "dashboard";
                } else {
                    // Toast rojo con barra de progreso según estándar Senior
                    Swal.fire({
                        icon: "error",
                        title: "Error de acceso",
                        text: res.message,
                        timer: 3000,
                        timerProgressBar: true,
                        confirmButtonColor: "#d33",
                    });
                }
            },
            complete: () => {
                $(ui.btnSubmit).prop("disabled", false).html("Ingresar");
            },
        });
    };

    return { init };
})();

$(document).ready(AuthModule.init);
