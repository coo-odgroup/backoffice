import $ from "jquery";
import { Modal } from "bootstrap";
// import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

let ajaxUrl = "http://127.0.0.1:8000/admin/";

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

export function initSelect2(selector, placeholder = "Select Option") {
    if ($(selector).length) {
        if ($(selector).hasClass("select2-hidden-accessible")) {
            // $(selector).select2('destroy');
        }

        $(selector).select2({
            theme: "bootstrap-5",
            width: "100%",
            placeholder: placeholder,
            dropdownAutoWidth: true,
            allowClear: true,
        });
    }
}

export function loadStateList(state_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-state-list",
        data: {
            state_id: state_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select State</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, state) {
                    let selected =
                        state_id > 0 && state.id == state_id ? "selected" : "";
                    options += `<option value="${state.id}" ${selected}>
                                        ${state.state_name}
                                    </option>`;
                });
            }

            $("#selState").html(options);
        },
        error: function (xhr) {
            console.log("Error loading states");
        },
    });
}

export function getDistrictList(state_id, selected_dist_id = 0) {
    $("#selDistrict").html('<option value="">Loading...</option>');

    if (!state_id) {
        $("#selDistrict").html(
            '<option value="">-- Select District --</option>',
        );
        return;
    }

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-district-list",
        data: {
            state_id: state_id,
            selected_dist_id: selected_dist_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select District --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (district) {
                    let selected =
                        district.id == selected_dist_id ? "selected" : "";

                    options += `
                            <option value="${district.id}" ${selected}>
                                ${district.district_name}
                            </option>
                        `;
                });
            }

            $("#selDistrict").html(options);
        },
        error: function (xhr) {
            console.log("Error loading districts");
            $("#selDistrict").html(
                '<option value="">-- Select District --</option>',
            );
        },
    });
}

export function initTableCheckbox(headerSelector, itemSelector) {
    $(document).on("change", headerSelector, function () {
        let isChecked = $(this).prop("checked");

        $(itemSelector).prop("checked", isChecked);
    });

    $(document).on("change", itemSelector, function () {
        let totalItems = $(itemSelector).length;
        let checkedItems = $(itemSelector + ":checked").length;

        if (checkedItems === totalItems && totalItems > 0) {
            $(headerSelector).prop("checked", true);
        } else {
            $(headerSelector).prop("checked", false);
        }
    });
}

export function actionRec(action) {
    let ids = [];

    $(".chkItem:checked").each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        viewAlert("Please select a record!");
        return;
    }

    let message = "";

    if (action === "D") message = "Are you sure to delete selected record(s)?";
    if (action === "A")
        message = "Are you sure to activate selected record(s)?";
    if (action === "UN")
        message = "Are you sure to inactivate selected record(s)?";

    confirmAlert(message, function () {
        executeBulkAction(ids, action);
    });
}

export function confirmAlert(message, callback) {
    const modalElement = document.getElementById("confirmModal");
    const modal = new Modal(modalElement);

    // Set message
    modalElement.querySelector(".confirmMessage").innerHTML = message;

    modal.show();

    const okBtn = modalElement.querySelector("#btnConfirmOk");

    // Remove previous handler
    okBtn.onclick = null;

    okBtn.onclick = function () {
        modal.hide();

        if (typeof callback === "function") {
            callback();
        }
    };
}

export function viewAlert(msg, ctrlId = "", redLoc = "") {
    const modalElement = document.getElementById("alertModal");
    const modal = new Modal(modalElement, {
        backdrop: "static",
        keyboard: false,
    });

    // Set message
    modalElement.querySelector(".alertMessage").innerHTML = msg;

    // Show modal
    modal.show();

    const okBtn = modalElement.querySelector("#btnAlertOk");

    // Remove previous click handler
    okBtn.onclick = null;

    okBtn.onclick = function () {
        modal.hide();

        if (ctrlId !== "") {
            document.getElementById(ctrlId)?.focus();
        }

        if (redLoc !== "" && redLoc !== "pr") {
            window.location.href = redLoc;
        }

        // if (redLoc === 'pr') {
        //     window.location.reload();
        // }
    };
}

export function executeBulkAction(ids, action) {
    let model = $("#hdn_model").val();

    $.ajax({
        type: "POST",
        url: window.bulkActionUrl,
        data: {
            ids: ids.join(","), // convert to string
            action: action,
            model: model,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            viewAlert(response.message);

            if (window.dataTableInstance) {
                setTimeout(function () {
                    window.dataTableInstance.ajax.reload(null, false);
                }, 300);
            }
        },
        error: function (xhr) {
            viewAlert("Something went wrong! Please try again later.");
        },
    });
}

export function checkFun(ctrId) {
    if ($("#" + ctrId).is(":checked")) {
        if ($(".chkItem:checked").length == $(".chkItem").length) {
            $(".chkAll").prop("checked", true);
        }
    } else {
        $(".chkAll").prop("checked", false);
    }
}

export function viewLogs(table, id) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "audit-logs",
        data: {
            table: table,
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let html = "";

            // console.log(response);

            if (response.length > 0) {
                $.each(response, function (index, log) {
                    html += `
                            <div class="card mb-1 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong>${log.action}</strong>
                                    </span>
                                    <span class="text-muted">
                                         ${formatDate(log.created_at)}
                                    </span>
                                </div>
                                   <div class="card-body">
                                     <table class="table table-bordered table-hover mb-0 align-middle table-md">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Field</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                                <th>Changed By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;

                    if (log.changes && log.changes.length > 0) {
                        $.each(log.changes, function (i, change) {
                            html += `
                                    <tr class="table-warning">
                                        <td><strong>${change.field}</strong></td>
                                        <td class="text-danger">
                                            ${change.old ?? "-"}
                                        </td>
                                        <td class="text-success">
                                            ${change.new ?? "-"}
                                        </td>
                                        <td>${log.created_by ?? "-"}</td>
                                    </tr>
                                `;
                        });
                    } else {
                        html += `
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        No field changes
                                    </td>
                                </tr>
                            `;
                    }

                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                });
            } else {
                html = `
                        <div class="alert alert-info text-center">
                            No audit logs found.
                        </div>
                    `;
            }

            $("#logContainer").html(html);
            const modalElement = document.getElementById("logModal");
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            // $('#logModal').modal('show');
        },

        error: function (xhr) {
            console.log("Error loading audit logs");
        },
    });
}

export function viewUserRecord(id) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "viewuser",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let html = "";

            // If single object
            if (response && Object.keys(response).length > 0) {
                html += `
                    <div class="card shadow-sm mb-2">
                        <div class="card-header bg-light">
                            <strong>Users Records</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover mb-0 align-middle">
                                <tbody>

                                    <tr>
                                        <th style="width:25%">Users ID</th>
                                        <td style="width:25%">${response.id ?? "-"}</td>

                                        <th style="width:25%">Unique ID</th>
                                        <td style="width:25%">${response.unique_id ?? "-"}</td>
                                    </tr>

                                    <tr>
                                        <th>User Name</th>
                                        <td>${response.name ?? "-"}</td>

                                        <th>Organization Name</th>
                                        <td>${response.organization_name ?? "-"}</td>
                                    </tr>

                                    <tr>
                                        <th>Primary Email</th>
                                        <td>${response.primary_email ?? "-"}</td>

                                        <th>Primary Contact</th>
                                        <td>${response.primary_contact ?? "-"}</td>
                                    </tr>

                                    <tr>
                                        <th>Location</th>
                                        <td>${response.location ?? "-"}</td>

                                        <th>User Role</th>
                                        <td>${response.user_role ?? "-"}</td>
                                    </tr>

                                    <tr>
                                        <th>Active Status</th>
                                        <td>${response.active_status == 1 ? "Active" : "Inactive"}</td>

                                        <th></th>
                                        <td></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                if (response.info) {
                    html += `
                        <div class="card shadow-sm mb-2">
                            <div class="card-header bg-light">
                                <strong>Users Info</strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <tbody>
                                        <tr>
                                            <th style="width:25%">Secondary Email</th>
                                            <td style="width:25%">${response.info.secondary_email ?? "-"}</td>

                                            <th style="width:25%">Secondary Contact</th>
                                            <td style="width:25%">${response.info.secondary_contact ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>Aadhaar No</th>
                                            <td>${response.info.aadhaar_no ?? "-"}</td>

                                            <th>PAN Card No</th>
                                            <td>${response.info.pancard_no ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>President Name</th>
                                            <td>${response.info.president_name ?? "-"}</td>

                                            <th>President Phone</th>
                                            <td>${response.info.president_phone ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>General Secretary Name</th>
                                            <td>${response.info.general_secretary_name ?? "-"}</td>

                                            <th>General Secretary Phone</th>
                                            <td>${response.info.general_secretary_phone ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>GST Available</th>
                                            <td>${response.info.has_gst == 1 ? "Yes" : "No"}</td>

                                            <th>GST No</th>
                                            <td>${response.info.gst_no ?? "-"}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }
                if (response.address) {
                    html += `
                        <div class="card shadow-sm mb-2">
                            <div class="card-header bg-light">
                                <strong>Users Address</strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <tbody>
                                        <tr>
                                            <th style="width:25%">Address</th>
                                            <td style="width:25%">${response.address.address ?? "-"}</td>

                                            <th style="width:25%">Street</th>
                                            <td style="width:25%">${response.address.street ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>Landmark</th>
                                            <td>${response.address.landmark ?? "-"}</td>

                                            <th>City</th>
                                            <td>${response.address.city ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>Pincode</th>
                                            <td>${response.address.pincode ?? "-"}</td>

                                            <th></th>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }
                if (response.bankdetails) {
                    html += `
                        <div class="card shadow-sm mb-2">
                            <div class="card-header bg-light">
                                <strong>Bank Details</strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <tbody>
                                        <tr>
                                            <th style="width:25%">Account Name</th>
                                            <td style="width:25%">${response.bankdetails.bank_account_name ?? "-"}</td>

                                            <th style="width:25%">Bank Name</th>
                                            <td style="width:25%">${response.bankdetails.bank_name ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>Account Number</th>
                                            <td>${response.bankdetails.bank_account_number ?? "-"}</td>

                                            <th>IFSC</th>
                                            <td>${response.bankdetails.bank_ifsc ?? "-"}</td>
                                        </tr>

                                        <tr>
                                            <th>Bank Address</th>
                                            <td>${response.bankdetails.bank_address ?? "-"}</td>

                                            <th>UPI ID</th>
                                            <td>${response.bankdetails.upi_id ?? "-"}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }
            } else {
                html = `
                    <div class="alert alert-info text-center">
                        No record found.
                    </div>
                `;
            }

            $("#viewUserRecordContainer").html(html);

            const modalElement = document.getElementById("viewUserRecord");
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        },

        error: function (xhr) {
            console.log("Error loading Users Record");
        },
    });
}

export function viewBusRecord(id) {
    $("#viewBusRecordContainer").html("Loading...");

    const modalElement = document.getElementById("viewBusRecord");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    $.ajax({
        type: "GET",
        url: ajaxUrl + "bus/create/preview/" + id + "/view",
        success: function (response) {
            $("#viewBusRecordContainer").html(response);
        },
        error: function () {
            $("#viewBusRecordContainer").html(`
                <div class="alert alert-danger text-center">
                    Failed to load preview
                </div>
            `);
        }
    });
}

export function formatDate(dateString) {
    let date = new Date(dateString);

    return date.toLocaleString("en-IN", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

export function loadCityList(selected_city_id = 0) {
    // console.log(selected_city_id);

    $(".selCity").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-city-list",
        data: {
            selected_city_id: selected_city_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select City --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (city) {
                    let selected =
                        city.id == selected_city_id ? "selected" : "";
                    options += `<option value="${city.id}" ${selected}>${city.city_name}</option>`;
                });
            }

            $(".selCity").html(options);
        },
        error: function () {
            $(".selCity").html('<option value="">-- Select City --</option>');
        },
    });
}

export function loadCityListSlugVal(selected_city_id = 0) {
    $(".citySlugVal").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-city-list",
        data: {
            selected_city_id: selected_city_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select City --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (city) {
                    let selected =
                        city.id == selected_city_id ? "selected" : "";
                    options += `<option value="${city.id}" data-alias="${city.alias}" ${selected}>
                                    ${city.city_name}
                                </option>`;
                });
            }

            $(".citySlugVal").html(options);
        },
        error: function () {
            $(".citySlugVal").html(
                '<option value="">-- Select City --</option>',
            );
        },
    });
}

$(document).on("change", ".order-input", function () {
    let value = $(this).val();
    let id = $(this).data("id");
    let table = $(this).data("table");
    let column = $(this).data("column");

    $.ajax({
        url: ajaxUrl + "update-sequence",
        type: "POST",
        data: {
            table: table,
            column: column,
            value: value,
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            viewAlert("Order updated successfully");
        },
        error: function (xhr) {
            console.log(xhr.responseText);
            viewAlert("Something went wrong");
        },
    });
});

export function loadAmenityCategory(cat_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-amenity-category-list",
        data: {
            cat_id: cat_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Amenity Category</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, cat) {
                    let selected =
                        cat_id > 0 && cat.id == cat_id ? "selected" : "";
                    options += `<option value="${cat.id}" ${selected}>
                                        ${cat.category_name}
                                    </option>`;
                });
            }

            $("#amenityCategory").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Amenity Category");
        },
    });
}

export function initTooltips() {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );

    tooltipTriggerList.map(function (tooltipTriggerEl) {
        // Dispose old tooltip (important for DataTables redraw)
        if (bootstrap.Tooltip.getInstance(tooltipTriggerEl)) {
            bootstrap.Tooltip.getInstance(tooltipTriggerEl).dispose();
        }

        return new bootstrap.Tooltip(tooltipTriggerEl, {
            html: true,
            sanitize: false,
        });
    });
}

export function loadApiAppsList(app_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-apiapps-list",
        data: {
            app_id: app_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Api App</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        app_id > 0 && app.id == app_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                        ${app.app_name}
                                    </option>`;
                });
            }

            $("#apiApp").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Api Apps");
        },
    });
}

export function loadParentList(parent_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-parent-module-list",
        data: {
            parent_id: parent_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="0">Select Parent Module</option>';

            if (response.status && response.data.length > 0) {
                $.each(response.data, function (i, module) {
                    let selected = parent_id == module.id ? "selected" : "";
                    options += `<option value="${module.id}" ${selected}>
                                    ${module.code}
                                </option>`;
                });
            }

            $("#selParent").html(options);
        },
    });
}

export function initCharCounter(fieldSelectors = []) {
    fieldSelectors.forEach((selector) => {
        let inputs = [];

        // If selector starts with . or [ → treat as querySelectorAll
        if (
            selector.startsWith(".") ||
            selector.startsWith("[") ||
            selector.includes("name=")
        ) {
            inputs = document.querySelectorAll(selector);
        } else {
            // Otherwise treat as ID (for backward compatibility)
            const element = document.getElementById(selector);
            if (element) {
                inputs = [element];
            }
        }

        if (!inputs.length) return;

        inputs.forEach((input) => {
            const counter = input.parentElement.querySelector(".char-counter");
            const maxLength = input.getAttribute("maxlength");

            if (!counter || !maxLength) return;

            const updateCounter = () => {
                const currentLength = input.value.length;
                counter.textContent = `Remaining ${currentLength}/${maxLength}`;
            };

            input.removeEventListener("input", updateCounter); // prevent duplicate binding
            input.addEventListener("input", updateCounter);

            updateCounter();
        });
    });
}

export function loadRoleList(role_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-role-list",
        data: {
            role_id: role_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select User Role</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        role_id > 0 && app.id == role_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                        ${app.name}
                                    </option>`;
                });
            }

            $("#userRole").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Roles");
        },
    });
}

export function loadBlogCategoryList(cat_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-blog-category-list",
        data: {
            cat_id: cat_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Blog Category</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        cat_id > 0 && app.id == cat_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                        ${app.category_name}
                                    </option>`;
                });
            }

            $("#blogCategory").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Blog Category");
        },
    });
}

export function loadFaqCategory(cat_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-faq-category-list",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.status === true) {
                let $dropdown = $("#faqCategory");

                $dropdown.empty();
                $dropdown.append(
                    '<option value="0">Select FAQ Category</option>',
                );

                $.each(response.data, function (index, item) {
                    let selected =
                        cat_id != 0 && cat_id == item.id ? "selected" : "";

                    $dropdown.append(
                        `<option value="${item.id}" ${selected}>
                            ${item.category_name}
                        </option>`,
                    );
                });

                if ($dropdown.hasClass("select2-hidden-accessible")) {
                    $dropdown.trigger("change");
                }
            } else {
                console.error("Failed to load FAQ categories");
            }
        },
        error: function (xhr) {
            console.error("AJAX Error:", xhr.responseText);
        },
    });
}

$(document).on("click", ".remove-image", function () {
    let button = $(this);
    let containerId = button.data("container");

    confirmAlert("Are you sure to proceed!", function () {
        $.ajax({
            url: ajaxUrl + "remove-image",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: button.data("id"),
                table: button.data("table"),
                column: button.data("column"),
                path: button.data("path"),
            },
            success: function (response) {
                if (response.status) {
                    $("#" + containerId).addClass("d-none");
                    viewAlert(response.message);
                } else {
                    viewAlert(response.message);
                }
            },
        });
    });
});

export function loadBlogList(blog_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-blog-list",
        data: {
            blog_id: blog_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Blog</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        blog_id > 0 && app.id == blog_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                        ${app.title}
                                    </option>`;
                });
            }

            $("#blog").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Blog");
        },
    });
}

export function loadBlogTagsList(tag_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-blogtags-list",
        data: {
            tag_id: tag_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Blog Tags</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        tag_id > 0 && app.id == tag_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.tag_name}
                                </option>`;
                });
            }

            $("#blogTags").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Blog Tags");
        },
    });
}

export function searchCity() {
    let city = $("#citySearch").val();

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-city-search",
        data: {
            city: city,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let html = "";

            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, city) {
                    html += `<div class="form-check mb-2">
                                <input class="form-check-input cityCheck"
                                       type="checkbox"
                                       value="${city.city_name}"
                                       onchange="toggleCity(this)">
                                <label class="form-check-label">
                                    ${city.city_name}
                                </label>
                            </div>`;
                });
            } else {
                html = `<p class="text-danger">No city found</p>`;
            }

            $("#cityList").html(html);
        },

        error: function () {
            console.log("Error loading cities");
        },
    });
}

export function loadPlacementList(selected_placement_id = 0) {
    $("#placement").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-placement-list",
        data: {
            selected_placement_id: selected_placement_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select Placement --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (placement) {
                    let selected =
                        placement.id == selected_placement_id ? "selected" : "";

                    options += `<option value="${placement.id}" ${selected}>${placement.name}</option>`;
                });
            }

            $("#placement").html(options);
        },
        error: function () {
            $("#placement").html(
                '<option value="">-- Select Placement --</option>',
            );
        },
    });
}

export function loadVendorList(selected_vendor_id = 0) {
    $("#vendor").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-vendor-list",
        data: {
            selected_vendor_id: selected_vendor_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select Vendor --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (vendor) {
                    let selected =
                        vendor.id == selected_vendor_id ? "selected" : "";

                    options += `<option value="${vendor.id}" ${selected}>${vendor.company_name}</option>`;
                });
            }

            $("#vendor").html(options);
        },
        error: function () {
            $("#vendor").html('<option value="">-- Select Vendor --</option>');
        },
    });
}

export function loadPricingPlanList(selected_plan_id = 0) {
    $("#pricingPlan").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-pricing-plan-list",
        data: {
            selected_plan_id: selected_plan_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select Pricing Plan --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (plan) {
                    let selected =
                        plan.id == selected_plan_id ? "selected" : "";

                    options += `<option value="${plan.id}" ${selected}>${plan.plan_name}</option>`;
                });
            }

            $("#pricingPlan").html(options);
        },
        error: function () {
            $("#pricingPlan").html(
                '<option value="">-- Select Pricing Plan --</option>',
            );
        },
    });
}

export function loadCampaignList(selected_campaign_id = 0) {
    $("#campaign").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-campaign-list",
        data: {
            selected_campaign_id: selected_campaign_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select Campaign --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (campaign) {
                    let selected =
                        campaign.id == selected_campaign_id ? "selected" : "";

                    options += `<option value="${campaign.id}" ${selected}>${campaign.title}</option>`;
                });
            }

            $("#campaign").html(options);
        },
        error: function () {
            $("#campaign").html(
                '<option value="">-- Select Campaign --</option>',
            );
        },
    });
}

export function loadCountryList(selected_country_id = 0) {
    $("#country").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-country-list",
        data: {
            selected_country_id: selected_country_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">-- Select Country --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (country) {
                    let selected =
                        country.id == selected_country_id ? "selected" : "";

                    options += `<option value="${country.id}" ${selected}>${country.name}</option>`;
                });
            }

            $("#country").html(options);
        },
        error: function () {
            $("#country").html(
                '<option value="">-- Select Country --</option>',
            );
        },
    });
}

export function loadBrandList(selected_brand_id = 0) {
    $("#brandSearch, #brand").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-brand-list",
        data: {
            selected_brand_id: selected_brand_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let options = '<option value="">-- Select Brand --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (brand) {
                    let selected =
                        brand.id == selected_brand_id ? "selected" : "";

                    options += `<option value="${brand.id}" ${selected}>${brand.brand_name}</option>`;
                });
            }

            $("#brandSearch, #brand").html(options);
        },

        error: function () {
            $("#brandSearch, #brand").html(
                '<option value="">-- Select Brand --</option>',
            );
        },
    });
}

export function loadCancellationslabList(slab_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-cancellationslab-list",
        data: {
            slab_id: slab_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Cancellation Slab</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        slab_id > 0 && app.id == slab_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.slab_name}
                                </option>`;
                });
            }

            $("#slab").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Cancellation Slab");
        },
    });
}

export function loadAnnexureTypeList(selected_id = 0) {
    $(".selAnnexureType").html('<option value="">Loading...</option>');

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-annexure-type-list",
        data: {
            selected_id: selected_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let options =
                '<option value="">-- Select Annexure Type --</option>';

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (item) {
                    let selected = item.id == selected_id ? "selected" : "";

                    options += `<option value="${item.id}" ${selected}>
                                    ${item.annexture_type}
                                                  </option>`;
                });
            }
            $(".selAnnexureType").html(options);
        },

        error: function () {
            $(".selAnnexureType").html(
                '<option value="">-- Select Annexure Type --</option>',
            );
        },
    });
}

export function loadBusModelsList(model_id = "", brand_id = "") {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-busmodels-list",
        data: {
            model_id: model_id,
            brand_id: brand_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Bus Model</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        model_id > 0 && app.id == model_id ? "selected" : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.model_name}
                                </option>`;
                });
            }

            $("#busModel").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Bus Model");
        },
    });
}

export function loadAxleTypeList(axle_typet_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-axletype-list",
        data: {
            axle_typet_id: axle_typet_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Axle Type</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        axle_typet_id > 0 && app.id == axle_typet_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.axle_type}
                                </option>`;
                });
            }

            $("#axleType").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Axle Type");
        },
    });
}

export function loadBusServicesList(bus_service_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-busservices-list",
        data: {
            bus_service_id: bus_service_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Bus Service</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        bus_service_id > 0 && app.id == bus_service_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.bus_service_name}
                                </option>`;
                });
            }

            $("#busService").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Bus Service");
        },
    });
}

export function loadSeatTypeList(seat_type_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-seattype-list",
        data: {
            seat_type_id: seat_type_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Seat Type</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        seat_type_id > 0 && app.id == seat_type_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.seat_type}
                                </option>`;
                });
            }

            $("#seatType").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Seat Type");
        },
    });
}

export function loadSeatLayoutList(seat_layout_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-seatlayout-list",
        data: {
            seat_layout_id: seat_layout_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Seat Layout</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        seat_layout_id > 0 && app.id == seat_layout_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.seat_layout}
                                </option>`;
                });
            }

            $("#seatLayout").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Seat Layout");
        },
    });
}

function getLoadAnnextureList(annexture_type = "", type = "") {
    let container = document.getElementById("offerValuesContainer");
    container.innerHTML = "";

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-annexture-list",
        data: {
            annexture_type: annexture_type,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.status && response.data.length > 0) {
                response.data.forEach((item) => {
                    let div = document.createElement("div");
                    div.className = "offer-chip";

                    div.innerText =
                        type === "PERCENTAGE"
                            ? item.annexture_name + "%"
                            : "₹" + item.annexture_name;

                    div.onclick = function () {
                        document
                            .querySelectorAll(".offer-chip")
                            .forEach((c) => c.classList.remove("active"));

                        div.classList.add("active");

                        document.querySelector(
                            'input[name="offer_value"]',
                        ).value = item.annexture_name;
                    };

                    container.appendChild(div);
                });
            } else {
                container.innerHTML = "<p>No Data Found</p>";
            }
        },
    });
}
export function loadAnnextureList_bk(key, selected = "", selector = ".annexture") {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-annexture-list",
        data: {
            annexture_type: key,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },

        success: function (response) {
            let html = `<option value="">Select</option>`;

            if (response.status && response.data.length > 0) {
                response.data.forEach((item) => {
                    let isSelected = selected == item.id ? "selected" : "";

                    html += `<option value="${item.id}" ${isSelected}>
                                ${item.annexture_name}
                             </option>`;
                });
            } else {
                html = `<option value="">No Data Found</option>`;
            }

            $(selector).html(html);

            commonAjax.initSelect2(selector, "Select");
        },

        error: function (err) {
            console.log("Annexure Error:", err);
        },
    });
}

// export function loadAnnextureList(annexture_type = "", selected_id = 0) {
//     $.ajax({
//         type: "POST",
//         url: ajaxUrl + "get-annexture-list",
//         data: {
//             annexture_type: annexture_type,
//             _token: $('meta[name="csrf-token"]').attr("content"),
//         },
//         dataType: "json",

//         success: function (response) {
//             let options = '<option value="">Select Option</option>';

//             if (response.status && response.data.length > 0) {
//                 $.each(response.data, function (index, item) {
//                     let selected = selected_id == item.annexture_value ? "selected" : "";

//                     options += `<option value="${item.annexture_value}" ${selected}>
//                                         ${item.annexture_name}
//                                     </option>`;
//                 });
//             }

//             $(".annexture").html(options);
//         },

//         error: function () {
//             console.log("Error loading annexture list");
//         },
//     });
// }

export function loadAnnextureList(annexture_types = [], callback = null) {

    $.ajax({

        type: "POST",

        url: ajaxUrl + "get-annexture-list",

        data: {
            annexture_types: annexture_types,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },

        dataType: "json",

        success: function(response) {

            if (!response.status) {
                return;
            }

            if (typeof callback === 'function') {
                callback(response.data);
            }

        },

        error: function() {
            console.log("Error loading annexture list");
        }

    });
}

export function loadCampaignMasterList(campaign_master_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-campaign-master-list",
        data: {
            campaign_master_id: campaign_master_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Campaign Master</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        campaign_master_id > 0 && app.id == campaign_master_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                        ${app.campaign_name}
                                    </option>`;
                });
            }

            $("#campaignMaster").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Campaign Master");
        },
    });
}

export function loadAmenityList(selected_ids = []) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-amenity-list",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let options = "";

            if (response.status && response.data.length > 0) {
                $.each(response.data, function (i, category) {
                    options += `<optgroup label="${category.category_name}">`;

                    $.each(category.amenities, function (j, amenity) {
                        let selected = selected_ids.includes(amenity.id)
                            ? "selected"
                            : "";

                        options += `<option value="${amenity.id}" ${selected}>
                                            ${amenity.name}
                                        </option>`;
                    });

                    options += `</optgroup>`;
                });
            }

            // set options
            $("#selAmenity").html(options);

            // init select2
            // $("#selAmenity").select2({
            //     placeholder: "Select Amenities",
            //     allowClear: true,
            //     width: '100%'
            // });
        },

        error: function (xhr) {
            console.log("Error loading amenities");
        },
    });
}

export function initClearableInputs() {
    $(".clearable").each(function () {
        let input = $(this);

        // prevent duplicate wrapping
        if (input.parent(".position-relative").length) return;

        input.wrap('<div class="position-relative"></div>');

        input.after(`
                <span class="clear-btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                    style="cursor:pointer; display:none;">
                    &times;
                </span>
            `);
    });

    // show/hide button
    $(document).on("keyup", ".clearable", function () {
        let btn = $(this).siblings(".clear-btn");
        btn.toggle($(this).val().length > 0);
    });

    // clear input
    $(document).on("click", ".clear-btn", function () {
        let input = $(this).siblings("input");
        input.val("").trigger("keyup").focus();
    });
}

export function loadBusOperatorList(bus_operator_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-busoperator-list",
        data: {
            bus_operator_id: bus_operator_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            let options = '<option value="">Select Bus Operator</option>';
            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected =
                        bus_operator_id > 0 && app.id == bus_operator_id
                            ? "selected"
                            : "";
                    options += `<option value="${app.id}" ${selected}>
                                    ${app.name}
                                </option>`;
                });
            }

            $("#busOperator").html(options);
        },
        error: function (xhr) {
            console.log("Error loading Bus Operator");
        },
    });
}

export function loadBusOperatorDropdown(selected_ids = []) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-busoperator-role-list",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let options = `<option value="">Select Bus Operator</option>`;

            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, app) {
                    let selected = selected_ids.includes(app.id)
                        ? "selected"
                        : "";

                    options += `<option value="${app.id}" ${selected}>
                                    ${app.organization_name || app.name}
                                </option>`;
                });
            }

            $("#operator").html(options);

            if (selected_ids.length === 0) {
                $("#operator").val("");
            }

            $("#operator").trigger("change");
        },

        error: function () {
            console.log("Error loading Bus Operator");
        },
    });
}

export function makeUpperCase(ids) {
    ids.forEach(function (id) {
        $(document).on("input", "#" + id, function () {
            $(this).val($(this).val().toUpperCase());
        });
    });
}

export function allowOnlyNumbers(ids) {
    ids.forEach(function (id) {
        $(document).on("input", "#" + id, function () {
            let value = $(this).val();

            // Remove non-numeric characters
            value = value.replace(/[^0-9]/g, "");

            // Limit to 10 digits
            value = value.slice(0, 10);

            $(this).val(value);
        });
    });
}

export function allowNumbersWithComma(ids) {
    ids.forEach(function (id) {
        $(document).on("input", "#" + id, function () {
            let value = $(this).val();

            // Allow only digits and commas
            value = value.replace(/[^0-9,]/g, "");

            // Split by comma, limit each number to 10 digits
            let numbers = value.split(",").map(num => num.slice(0, 10));

            // Join back with comma
            value = numbers.join(",");

            $(this).val(value);
        });
    });
}

export function loadTicketFareSlabList(selector, selected = null) {
    $.ajax({
        type: "GET",
        url: ajaxUrl + "ticket-fare-slab/list",
        dataType: "json",

        success: function (res) {
            let html = '<option value="">Select Ticket Fare Slab</option>';

            if (res.status && res.data.length > 0) {
                $.each(res.data, function (i, item) {
                    let isSelected = selected == item.id ? "selected" : "";

                    html += `<option value="${item.id}" ${isSelected}>
                                ${item.slab_name}
                             </option>`;
                });
            }

            $(selector).html(html).trigger("change");
        },

        error: function () {
            console.log("Error loading Ticket Fare Slab");
        },
    });
}

export function loadBusListByOperator(selector, operator_id, selected = null) {
    $.ajax({
        type: "POST",
        url: "/admin/get-buses-by-operator",
        data: {
            operator_id: operator_id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },

        success: function (res) {
            console.log("Bus Data:", res);

            let html = '<option value="">Select Bus</option>';

            if (res.status && res.data.length > 0) {
                res.data.forEach((item) => {
                    html += `<option value="${item.id}">
                                ${item.name} (${item.bus_number})
                             </option>`;
                });
            } else {
                html += `<option value="">No Bus Found</option>`;
            }

            $(selector).html(html).trigger("change");
        },
    });
}

export function loadUsersList(user_code = "", selected_id = 0) {
    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-users-list",
        data: {
            user_code: user_code,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",

        success: function (response) {
            let options = '<option value="">Select Option</option>';

            if (response.status && response.data.length > 0) {
                $.each(response.data, function (index, item) {
                    let selected = selected_id == item.id ? "selected" : "";

                    options += `<option value="${item.id}" ${selected}>
                                    ${item.name}${item.organization_name ? ` ( ${item.organization_name} )` : ''}
                                </option>`;
                });
            }

            $(".users").html(options);
        },

        error: function () {
            console.log("Error loading annexture list");
        },
    });
}
