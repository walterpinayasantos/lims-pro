$(document).ready(function () {
    "use strict";

    $("#pat_form_edit").on("submit", function (e) {
        e.preventDefault();

        let btn = $("#pat_btn_update");
        let originalText = btn.html();
        btn.prop("disabled", true).html('<i class="ti ti-loader animate-spin"></i> Guardando...');

        $.ajax({
            url: window.BASE_URL + "patient/update",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert("✅ " + res.message);
                    window.location.href = window.BASE_URL + "patient";
                } else {
                    alert("⚠️ " + res.message);
                    btn.prop("disabled", false).html(originalText);
                }
            },
            error: function (xhr) {
                alert("❌ Error al actualizar");
                btn.prop("disabled", false).html(originalText);
            },
        });
    });
});
