import $ from 'jquery';
import { Modal } from 'bootstrap';

let ajaxUrl = 'http://127.0.0.1:8000/admin/';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    export function initSelect2(selector, placeholder = "Select Option") {

        if ($(selector).length) {

            if ($(selector).hasClass("select2-hidden-accessible")) {
                $(selector).select2('destroy');
            }

            $(selector).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                dropdownAutoWidth: true
            });
        }
    }

    export function loadStateList(state_id = 0) {

        $.ajax({
            type: "POST",
            url: ajaxUrl + "get-state-list",
            data: {
                state_id: state_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",           
            success: function(response) {
                let options = '<option value="">Select State</option>';
                if(response.status && response.data.length > 0) {

                    $.each(response.data, function(index, state) {
                        let selected = (state_id > 0 && state.id == state_id) ? 'selected' : '';
                        options += `<option value="${state.id}" ${selected}>
                                        ${state.state_name}
                                    </option>`;
                    });
                }

                $('#selState').html(options);             
            },           
            error: function(xhr) {
                console.log("Error loading states");
            }
        });
    }

    export function getDistrictList(state_id, selected_dist_id = 0) {

        $('#selDistrict').html('<option value="">Loading...</option>');

        if (!state_id) {
            $('#selDistrict').html('<option value="">-- Select District --</option>');
            return;
        }

        $.ajax({
            type: "POST",
            url: ajaxUrl + "get-district-list",
            data: {
                state_id: state_id,
                selected_dist_id: selected_dist_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",         
            success: function(response) {

                let options = '<option value="">-- Select District --</option>';

                if (response.status && response.data.length > 0) {

                    response.data.forEach(function(district) {

                        let selected = (district.id == selected_dist_id) 
                                        ? 'selected' : '';

                        options += `
                            <option value="${district.id}" ${selected}>
                                ${district.district_name}
                            </option>
                        `;
                    });
                }

                $('#selDistrict').html(options);               
            },           
            error: function(xhr) {
                console.log("Error loading districts");
                $('#selDistrict').html('<option value="">-- Select District --</option>');
            }
        });
    }


    export function initTableCheckbox(headerSelector, itemSelector) {

        // ✅ Header → Select All
        $(document).on('change', headerSelector, function () {

            let isChecked = $(this).prop('checked');

            $(itemSelector).prop('checked', isChecked);

        });

        // ✅ Row Checkbox → Sync Header
        $(document).on('change', itemSelector, function () {

            let totalItems = $(itemSelector).length;
            let checkedItems = $(itemSelector + ':checked').length;

            if (checkedItems === totalItems && totalItems > 0) {
                $(headerSelector).prop('checked', true);
            } else {
                $(headerSelector).prop('checked', false);
            }

        });
    }

    export function actionRec(action) {

        let ids = [];

        $('.chkItem:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            viewAlert('Please select a record!');
            return;
        }

        let message = '';

        if (action === 'D') message = 'Are you sure to delete selected record(s)?';
        if (action === 'A') message = 'Are you sure to activate selected record(s)?';
        if (action === 'UN') message = 'Are you sure to inactivate selected record(s)?';

        confirmAlert(message, function () {

            executeBulkAction(ids, action);

        });
    }


   export function confirmAlert(message, callback) {

        const modalElement = document.getElementById('confirmModal');
        const modal = new Modal(modalElement);

        // Set message
        modalElement.querySelector('.confirmMessage').innerHTML = message;

        modal.show();

        const okBtn = modalElement.querySelector('#btnConfirmOk');

        // Remove previous handler
        okBtn.onclick = null;

        okBtn.onclick = function () {
            modal.hide();

            if (typeof callback === 'function') {
                callback();
            }
        };
    }

    export function viewAlert(msg, ctrlId = '', redLoc = '') {

        const modalElement = document.getElementById('alertModal');
        const modal = new Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        // Set message
        modalElement.querySelector('.alertMessage').innerHTML = msg;

        // Show modal
        modal.show();

        const okBtn = modalElement.querySelector('#btnAlertOk');

        // Remove previous click handler
        okBtn.onclick = null;

        okBtn.onclick = function () {

            modal.hide();

            if (ctrlId !== '') {
                document.getElementById(ctrlId)?.focus();
            }

            if (redLoc !== '' && redLoc !== 'pr') {
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
                ids: ids.join(','), // convert to string
                action: action,
                model: model,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                viewAlert(response.message);

                if (window.dataTableInstance) {
                    setTimeout(function () {
                        window.dataTableInstance.ajax.reload(null, false);
                    },300);
                }
            },
            error: function (xhr) {
                viewAlert('Something went wrong! Please try again later.');
                //console.log(xhr.responseText);
            }
        });
    }

    export function checkFun(ctrId) {
        if ($("#" + ctrId).is(':checked')) {
            if ($('.chkItem:checked').length == $('.chkItem').length) {
                $('.chkAll').prop('checked', true);
            }
        } else {
            $('.chkAll').prop('checked', false);
        }
    }
