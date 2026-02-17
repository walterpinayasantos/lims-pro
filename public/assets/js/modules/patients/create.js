$(document).ready(function () {
    "use strict";

    // 1. Copiar datos a facturación
    $("#pat_check_copy").on("change", function () {
        if (this.checked) {
            $("#nit_ci_invoice").val($("#document_id").val());
            let fullName = $("#last_name").val() + " " + $('input[name="first_name"]').val();
            $("#invoice_name").val(fullName);
        } else {
            $("#nit_ci_invoice").val("");
            $("#invoice_name").val("");
        }
    });

    // 2. Envío del formulario
    $("#pat_form_create").on("submit", function (e) {
        e.preventDefault();

        let btn = $("#pat_btn_save");
        let originalText = btn.html();
        btn.prop("disabled", true).html('<i class="ti ti-loader animate-spin"></i> Guardando...');

        $.ajax({
            // ✅ APLICANDO LA REGLA: URL EN SINGULAR
            url: window.BASE_URL + "patient/store",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert("✅ " + res.message);
                    // ✅ REDIRECCIÓN EN SINGULAR
                    window.location.href = window.BASE_URL + "patient";
                } else {
                    alert("⚠️ " + res.message);
                    btn.prop("disabled", false).html(originalText);
                }
            },
            error: function (xhr) {
                let msg = "Error desconocido";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert("❌ " + msg);
                btn.prop("disabled", false).html(originalText);
            },
        });
    });
});
