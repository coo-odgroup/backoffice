ajaxUrl = 'http://127.0.0.1:8000/admin/';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function initSelect2(selector, placeholder = "Select Option") {

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



function loadStateList() {

    $.ajax({
        type: "POST",
        url: ajaxUrl + "get-state-list",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function(response) {
            let options = '<option value="">Select State</option>';
            if(response.status && response.data.length > 0) {

                $.each(response.data, function(index, state) {
                    options += `<option value="${state.id}">
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

function getDistrictList(state_id, selected_dist_id = 0) {

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
