jQuery(document).ready(function($){
    var base_url = window.location.href.split('&')[0];
    const SHIPPER_FIELD = WPSTAjax.customer_field.shipper;

    $('.wpst-color-field').wpColorPicker();

    $('#wpst-navigation').on('click', '.btn', function(){
        let tab_container = $(this).data('tab_container');
        let current_tab = $(this).data('tab');
        let newUrl = base_url+'&tab='+current_tab;
        change_current_url(newUrl);

        $('#shiptrack-admin .tab-container').removeClass('active');
        $('#shiptrack-admin').find(tab_container).addClass('active');
        $('#wpst-navigation .btn').removeClass('active');
        $(this).addClass('active');
    }); 
    $('.wpst-sub-navigation').on('click', '.btn', function(){
        let base_url = window.location.href.split('&');
        base_url = base_url[0]+'&'+base_url[1];
        let tab_container = $(this).find('.options').data('tab_container');
        let current_tab = $(this).find('.options').data('tab');
        let newUrl = base_url+'&sub='+current_tab;
        change_current_url(newUrl);

        let container = $(this).closest('.wpst-sub-container');
        container.find('.wpst-sub-content').removeClass('active');
        container.find(tab_container).addClass('active');
        $(this).closest('.wpst-sub-navigation').find('.btn').removeClass('active');
        $(this).addClass('active');
    });
    
    function change_current_url(newUrl) {
        if (newUrl) {
            history.replaceState({}, null, newUrl);
        }
    }

    // Generate Report
    // Generate Report
    $('#wpst_export_form').on('submit', function(e){
        e.preventDefault();
        let date_from = $(this).find('#date_from').val();
        let date_to = $(this).find('#date_to').val();
        let status = $(this).find('#shiptrack_status').val();
        let shipper = $(this).find('#'+SHIPPER_FIELD.key).val();
        let assigned_client = $(this).find('#assigned_client').val();

        if (date_from && date_to) {
            $.post({
                url: WPSTAjax.ajaxurl,
                data: {
                    action: 'generate_report',
                    date_from,
                    date_to,
                    status,
                    shipper,
                    assigned_client
                },
                beforeSend: function() {
                    show_loading();
                },
                success: function(response) {
                    data = JSON.parse(response);
                    $('#wpst_export_form .alert').removeClass('d-none alert-danger alert-success');
                    if (data.status == 'error') {
                        $('#wpst_export_form .alert').addClass('alert-danger');
                        $('#wpst_export_form .alert').html(data.error);
                    } else {
                        $('#wpst_export_form .alert').addClass('alert-success');
                        $('#wpst_export_form .alert').html(data.msg);
                        download_file(data.fileurl, data.filename);
                    }
                    hide_loading();
                }
            });
        }
    });

    $('#post_per_page').on('change', function(){
        if ($(this).val()) {
            $(this).closest('form').trigger('submit');
        }
    });
});