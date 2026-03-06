/**
 * LIMS ARCHITECT PRO - Module: Exams Catalog
 * Versión Senior: DataTables, Modales Asíncronos, Cálculos y Teclado Científico
 */

const ExamCatalog = {
    // Símbolos científicos para el mini-teclado
    symbols: ["µ", "α", "β", "γ", "Δ", "≤", "≥", "±", "≈", "°", "10³", "10⁶", "10¹²"],

    // NUEVO: Operadores matemáticos para fórmulas
    operators: ["(", ")", "+", "-", "*", "/", "=", "^", "%", "."],

    init: function () {
        console.log("Exam Catalog Module Initialized");
        this.initDataTables();
        this.bindEvents();
        this.setupSymbolPickers();
    },

    /**
     * CORRECCIÓN: Función para insertar texto en la posición exacta del cursor
     * Evita que el texto se sobrescriba o se pierda el foco del input.
     */
    insertAtCursor: function (input, text) {
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const val = input.value;
        input.value = val.slice(0, start) + text + val.slice(end);

        // Reposicionar el cursor después de la inserción
        const newPos = start + text.length;
        input.setSelectionRange(newPos, newPos);
        input.focus();
    },

    /**
     * Inicializa DataTables con el diseño tradicional (Greeva Style)
     */
    initDataTables: function () {
        if (window.jQuery && jQuery.fn.DataTable) {
            $(".datatable-exams").each(function () {
                if (!$.fn.dataTable.isDataTable(this)) {
                    $(this).DataTable({
                        pageLength: 10,
                        destroy: true,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
                        },
                        // "l" registros izquierda, "f" buscador derecha
                        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rt<"row mt-3"<"col-sm-5"i><"col-sm-7"p>>',
                        drawCallback: function () {
                            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                            ExamCatalog.initTooltips();
                        },
                    });
                }
            });

            $('a[data-bs-toggle="pill"]').on("shown.bs.tab", function () {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        } else {
            setTimeout(() => this.initDataTables(), 100);
        }
    },

    /**
     * Manejo de eventos: Clic en editar y apertura de Modal
     */
    bindEvents: function () {
        const self = this;

        $(document).on("click", ".btn-edit-exam", function (e) {
            e.preventDefault();
            const id = $(this).data("id");
            const row = $(this).closest("tr");
            self.openEditModal(id, row);
        });

        $(document).on("input", "#helper_years", function () {
            const years = parseFloat($(this).val());
            if (!isNaN(years)) {
                // Cálculo estándar: años * 365
                const days = Math.round(years * 365);
                $("#helper_days_result").val(days);
            } else {
                $("#helper_days_result").val("");
            }
        });

        // Ayuda extra: Al hacer clic en el botón de copia, lo pega en el último input de edad enfocado
        let lastAgeInput = null;
        $(document).on("focus", 'input[name="age_min[]"], input[name="age_max[]"]', function () {
            lastAgeInput = $(this);
        });

        $(document).on("click", "#btnApplyDays", function () {
            const days = $("#helper_days_result").val();
            if (days && lastAgeInput) {
                lastAgeInput.val(days);
                lastAgeInput.focus();
            }
        });

        // Añadir nueva fila de rango dinámicamente
        $(document).on("click", "#btnAddRowRange", function () {
            $(".empty-row").remove();
            const newRow = `<tr>
                <td>
                    <select name="gender[]" class="form-select form-select-xs border-0">
                        <option value="B">Ambos</option>
                        <option value="M">M</option>
                        <option value="F">F</option>
                    </select>
                </td>
                <td><input type="number" name="age_min[]" value="0" class="form-control form-control-xs border-0"></td>
                <td><input type="number" name="age_max[]" value="36500" class="form-control form-control-xs border-0"></td>
                <td><input type="text" name="range_min[]" class="form-control form-control-xs border-0" placeholder="0.00"></td>
                <td><input type="text" name="range_max[]" class="form-control form-control-xs border-0" placeholder="0.00"></td>
                <td class="text-end">
                    <button type="button" class="btn btn-link text-danger p-0 btnDeleteRow"><i class="ti ti-trash"></i></button>
                </td>
            </tr>`;
            $("#tableRanges tbody").append(newRow);
        });

        // Eliminar fila de rango
        $(document).on("click", ".btnDeleteRow", function () {
            $(this).closest("tr").remove();
            if ($("#tableRanges tbody tr").length === 0) {
                $("#tableRanges tbody").append(
                    '<tr class="empty-row text-center"><td colspan="5" class="py-3 text-muted">No hay rangos definidos</td></tr>',
                );
            }
        });

        // Función para habilitar edición de una fila existente
        $(document).on("click", ".btnEditRow", function () {
            const row = $(this).closest("tr");
            // Habilitar todos los inputs y selects de la fila
            row.find("input, select").prop("disabled", false).removeClass("bg-transparent");
            // Cambiar el estilo del botón para indicar que está activo
            $(this).fadeOut(200);
        });
    },

    /**
     * Inicializa Tooltips de Bootstrap
     */
    initTooltips: function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            const oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            if (oldTooltip) oldTooltip.dispose();
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    /**
     * Abre el modal de Greeva y carga el formulario vía AJAX
     */
    openEditModal: function (id, row) {
        const modalElement = document.getElementById("modalEditExam");
        const modalBody = document.getElementById("modalBodyExam");
        const modalTitle = document.getElementById("modalTitle");
        const modalInstance = new bootstrap.Modal(modalElement);

        const examName = $(row).find("td:first").text().trim();
        modalTitle.innerHTML = `Editar Examen: <span class="text-primary">${examName}</span>`;

        modalBody.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>';
        modalInstance.show();

        fetch(`${BASE_URL}exam/edit_modal/${id}`)
            .then((response) => {
                if (!response.ok) throw new Error("Módulo no encontrado");
                return response.text();
            })
            .then((html) => {
                modalBody.innerHTML = html;
                this.setupFormSubmit(id, row, modalInstance);
                this.setupSymbolPickers(); // Inicializar teclados en el contenido cargado
            })
            .catch((error) => {
                modalBody.innerHTML = `<div class="alert alert-danger">Error 404: No se pudo cargar el formulario.</div>`;
            });
    },

    /**
     * Maneja el envío del formulario dentro del modal (Asíncrono)
     */
    setupFormSubmit: function (id, row, modalInstance) {
        const form = document.getElementById("formEditExam");
        if (!form) return;

        form.addEventListener("submit", (e) => {
            e.preventDefault();
            const btnSave = form.querySelector('button[type="submit"]');
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

            const formData = new FormData(form);

            fetch(`${BASE_URL}exam/update_ajax`, {
                method: "POST",
                body: formData,
            })
                .then((res) => res.json())
                .then((data) => {
                    const dt = $(row).closest("table").DataTable();

                    // Creamos un indicador visual si tiene código LOINC
                    const loinc = formData.get("loinc_code");
                    const loincBadge = loinc
                        ? ` <span class="badge bg-secondary-subtle text-secondary small">${loinc}</span>`
                        : "";

                    const nameHtml = `<strong>${formData.get("name")}</strong>${loincBadge}`;
                    const unitHtml = `<code>${formData.get("unit")}</code>`;
                    const typeHtml = `<span class="badge bg-light text-dark text-uppercase" style="font-size: 0.7rem;">${formData.get("result_type")}</span>`;

                    dt.cell(row, 0).data(nameHtml);
                    dt.cell(row, 1).data(unitHtml);
                    dt.cell(row, 2).data(typeHtml);
                    dt.draw(false);

                    modalInstance.hide();

                    if (data.status === "success") {
                        const dt = $(row).closest("table").DataTable();
                        const nameHtml = `<strong>${formData.get("name")}</strong>`;
                        const unitHtml = `<code>${formData.get("unit")}</code>`;
                        const typeHtml = `<span class="badge bg-light text-dark text-uppercase" style="font-size: 0.7rem;">${formData.get("result_type")}</span>`;

                        dt.cell(row, 0).data(nameHtml);
                        dt.cell(row, 1).data(unitHtml);
                        dt.cell(row, 2).data(typeHtml);
                        dt.draw(false);

                        modalInstance.hide();
                        if ($.NotificationApp) {
                            $.NotificationApp.send("¡Éxito!", "Examen actualizado", "top-right", "#5ba035", "success");
                        }
                    }
                })
                .catch((err) => console.error(err))
                .finally(() => {
                    btnSave.disabled = false;
                    btnSave.innerText = "Guardar Cambios";
                });
        });
    },

    /**
     * Teclado Científico Profesional
     * CORRECCIÓN: Botón Borrar mejorado para evitar cierres accidentales
     */
    setupSymbolPickers: function () {
        const inputs = document.querySelectorAll(".symbol-input");
        const numRow = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];

        inputs.forEach((input) => {
            // Desactivar autocompletado del navegador para evitar el historial negro
            input.setAttribute("autocomplete", "off");

            if (input.dataset.pickerInit) return;
            input.dataset.pickerInit = "true";

            const wrapper = input.closest(".mb-3") || input.parentNode;
            wrapper.style.position = "relative";

            const dropdown = document.createElement("div");
            dropdown.className = "symbol-keyboard shadow-lg border rounded-3 p-2 bg-white";
            dropdown.style.display = "none";
            dropdown.style.position = "absolute";
            dropdown.style.bottom = "100%"; // Posicionar arriba del input para visibilidad
            dropdown.style.left = "0";
            dropdown.style.zIndex = "1060";
            dropdown.style.width = "100%";
            dropdown.style.minWidth = "320px";
            dropdown.style.marginBottom = "5px";

            const createRow = (items, isSpecial = false) => {
                const row = document.createElement("div");
                row.className = "d-flex justify-content-center gap-1 mb-1";
                items.forEach((text) => {
                    const btn = document.createElement("button");
                    btn.type = "button"; // Evitar submit accidental
                    btn.className = `btn btn-sm ${isSpecial ? "btn-light border" : "btn-outline-primary"} flex-fill`;
                    btn.innerText = text;
                    btn.addEventListener("mousedown", (e) => {
                        e.preventDefault();
                        this.insertAtCursor(input, text);
                    });
                    row.appendChild(btn);
                });
                return row;
            };

            dropdown.appendChild(createRow(numRow, true));
            dropdown.appendChild(createRow(this.operators, true));

            const symbolGroups = [this.symbols.slice(0, 7), this.symbols.slice(7)];
            symbolGroups.forEach((group) => dropdown.appendChild(createRow(group)));

            const actionRow = document.createElement("div");
            actionRow.className = "d-flex justify-content-center gap-1 mt-1";

            const spaceBtn = document.createElement("button");
            spaceBtn.type = "button"; // Definir explícitamente como botón
            spaceBtn.className = "btn btn-sm btn-light border flex-grow-1";
            spaceBtn.innerText = "Espacio";
            spaceBtn.onmousedown = (e) => {
                e.preventDefault();
                this.insertAtCursor(input, " ");
            };

            const backBtn = document.createElement("button");
            backBtn.type = "button"; // FIX: Evita que el navegador lo tome como submit
            backBtn.className = "btn btn-sm btn-danger";
            backBtn.innerHTML = '<i class="ti ti-backspace"></i>';

            // Lógica de borrado robusta
            backBtn.onmousedown = (e) => {
                e.preventDefault();
                e.stopPropagation(); // Evitar que el evento suba al formulario

                const start = input.selectionStart;
                const end = input.selectionEnd;

                if (start === end) {
                    // Borrar un solo carácter a la izquierda
                    if (start > 0) {
                        const val = input.value;
                        input.value = val.slice(0, start - 1) + val.slice(start);
                        input.setSelectionRange(start - 1, start - 1);
                    }
                } else {
                    // Borrar selección de texto
                    const val = input.value;
                    input.value = val.slice(0, start) + val.slice(end);
                    input.setSelectionRange(start, start);
                }
                input.focus();
            };

            actionRow.appendChild(spaceBtn);
            actionRow.appendChild(backBtn);
            dropdown.appendChild(actionRow);

            wrapper.appendChild(dropdown);

            input.onfocus = () => (dropdown.style.display = "block");
            input.onblur = () => (dropdown.style.display = "none");
        });
    },
};

document.addEventListener("DOMContentLoaded", () => {
    ExamCatalog.init();
});
