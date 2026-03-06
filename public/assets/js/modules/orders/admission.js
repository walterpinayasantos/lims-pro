/**
 * LIMS ARCHITECT PRO - Módulo de Admisión
 * Maneja la lógica de carrito de compras y selección de pacientes.
 */
(function ($) {
    "use strict";

    // Estado del Carrito
    let cart = [];
    const elements = {
        patientSelect: $("#adm_patient_select"),
        testSelect: $("#adm_test_select"),
        cartBody: $("#adm_cart_body"),
        emptyRow: $("#adm_empty_row"),
        discountInput: $("#adm_input_discount"),
        paidInput: $("#adm_input_paid"),
        btnSave: $("#adm_btn_save"),
        displaySubtotal: $("#adm_display_subtotal"),
        displayTotal: $("#adm_display_total"),
        displayBalance: $("#adm_display_balance"),
    };

    // Inicialización
    $(document).ready(function () {
        initPatientSearch();
        initTestSearch();
        initCalculations();
        initSaveAction();
    });

    // 1. Configuración Select2 Pacientes
    function initPatientSearch() {
        elements.patientSelect.select2({
            placeholder: "Buscar por Nombre o CI...",
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: window.BASE_URL + "order/searchPatient",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
            },
        });
    }

    // 2. Configuración Select2 Exámenes
    function initTestSearch() {
        elements.testSelect.select2({
            placeholder: "Buscar Examen (Glucosa, Hemograma, etc)...",
            allowClear: true,
            minimumInputLength: 1, // Permite buscar con 1 letra
            ajax: {
                url: window.BASE_URL + "order/searchTest",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
            },
        });

        // Evento al seleccionar un examen
        elements.testSelect.on("select2:select", function (e) {
            const data = e.params.data;
            addTestToCart(data);
            // Limpiar selección para permitir agregar otro rápido
            $(this).val(null).trigger("change");
        });
    }

    // 3. Lógica del Carrito (Agregar/Quitar)
    function addTestToCart(testData) {
        // Evitar duplicados
        if (cart.some((item) => item.id == testData.id)) {
            // Usamos Toastr o Alert simple si no hay librería
            alert("¡Este examen ya está en la lista!");
            return;
        }

        // Agregar al array lógico
        cart.push({
            id: testData.id,
            name: testData.text,
            price: parseFloat(testData.price),
        });

        renderCart();
    }

    window.removeTestFromCart = function (index) {
        cart.splice(index, 1);
        renderCart();
    };

    function renderCart() {
        elements.cartBody.empty();

        if (cart.length === 0) {
            elements.cartBody.append(`
                <tr id="adm_empty_row">
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="ti ti-shopping-cart-off fs-2"></i><br>No hay exámenes seleccionados
                    </td>
                </tr>
            `);
        } else {
            cart.forEach((item, index) => {
                const row = `
                    <tr>
                        <td><span class="badge bg-light text-dark">TEST-${item.id}</span></td>
                        <td>${item.name}</td>
                        <td class="text-end fw-bold">${item.price.toFixed(2)}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-soft-danger" onclick="removeTestFromCart(${index})">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                elements.cartBody.append(row);
            });
        }
        recalculateTotals();
    }

    // 4. Cálculos Matemáticos (Descuentos y Saldos)
    function recalculateTotals() {
        let subtotal = cart.reduce((sum, item) => sum + item.price, 0);
        let discountPercent = parseFloat(elements.discountInput.val()) || 0;

        // Validar lógica de descuento
        if (discountPercent > 100) discountPercent = 100;
        if (discountPercent < 0) discountPercent = 0;

        let discountAmount = (subtotal * discountPercent) / 100;
        let total = subtotal - discountAmount;

        let paidAmount = parseFloat(elements.paidInput.val()) || 0;

        // Si paga más del total, ajustamos (o permitimos propina? no, LIMS estricto)
        if (paidAmount > total) paidAmount = total;

        let balance = total - paidAmount;

        // Actualizar UI
        elements.displaySubtotal.text(subtotal.toFixed(2) + " Bs");
        elements.displayTotal.text(total.toFixed(2) + " Bs");
        elements.displayBalance.text(balance.toFixed(2) + " Bs");

        // Color visual para saldo
        if (balance > 0) {
            elements.displayBalance.removeClass("text-success").addClass("text-danger");
        } else {
            elements.displayBalance.removeClass("text-danger").addClass("text-success");
        }
    }

    function initCalculations() {
        elements.discountInput.on("input", recalculateTotals);
        elements.paidInput.on("input", recalculateTotals);
    }

    // 5. Guardado (AJAX)
    function initSaveAction() {
        elements.btnSave.on("click", function () {
            // Validaciones previas
            if (!elements.patientSelect.val()) {
                alert("Por favor seleccione un paciente.");
                return;
            }
            if (cart.length === 0) {
                alert("Debe agregar al menos un examen.");
                return;
            }

            const payload = {
                patient_id: elements.patientSelect.val(),
                doctor_name: $("#adm_doctor_name").val(),
                items: cart.map((i) => i.id),
                discount_percent: elements.discountInput.val(),
                paid_amount: elements.paidInput.val(),
                payment_method: $("#adm_payment_method").val(),
            };

            // Bloquear botón
            const originalBtnText = elements.btnSave.html();
            elements.btnSave
                .prop("disabled", true)
                .html('<span class="spinner-border spinner-border-sm"></span> Procesando...');

            $.ajax({
                url: window.BASE_URL + "order/store",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(payload),
                success: function (response) {
                    if (response.success) {
                        // Éxito: Redirigir al comprobante
                        window.location.href = response.redirect;
                    }
                },
                error: function (xhr) {
                    let msg = "Error desconocido";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert("Error: " + msg);
                    elements.btnSave.prop("disabled", false).html(originalBtnText);
                },
            });
        });
    }
})(jQuery);
