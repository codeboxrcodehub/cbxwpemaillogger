(function ($) {
    'use strict';

    //var comfortsmtp_setting_awn_options = null ;

    $(document).ready(function () {
        var comfortsmtp_setting_awn_options = {
            labels: {
                tip: comfortsmtp_setting.awn_options.tip,
                info: comfortsmtp_setting.awn_options.info,
                success: comfortsmtp_setting.awn_options.success,
                warning: comfortsmtp_setting.awn_options.warning,
                alert: comfortsmtp_setting.awn_options.alert,
                async: comfortsmtp_setting.awn_options.async,
                confirm: comfortsmtp_setting.awn_options.confirm,
                confirmOk: comfortsmtp_setting.awn_options.confirmOk,
                confirmCancel: comfortsmtp_setting.awn_options.confirmCancel,
            }
        };

        $('.setting-color-picker-wrapper').each(function (index, element) {
            var $color_field_wrap = $(element);
            var $color_field = $color_field_wrap.find('.setting-color-picker');
            var $color_field_fire = $color_field_wrap.find(
                '.setting-color-picker-fire'
            );

            var $current_color = $color_field_fire.data('current-color');
            //var $default_color = $color_field_fire.data('default-color');

            // Simple example, see optional options for more configuration.
            const pickr = Pickr.create({
                el: $color_field_fire[0],
                theme: 'classic', // or 'monolith', or 'nano'
                default: $current_color,

                swatches: [
                    'rgba(244, 67, 54, 1)',
                    'rgba(233, 30, 99, 0.95)',
                    'rgba(156, 39, 176, 0.9)',
                    'rgba(103, 58, 183, 0.85)',
                    'rgba(63, 81, 181, 0.8)',
                    'rgba(33, 150, 243, 0.75)',
                    'rgba(3, 169, 244, 0.7)',
                    'rgba(0, 188, 212, 0.7)',
                    'rgba(0, 150, 136, 0.75)',
                    'rgba(76, 175, 80, 0.8)',
                    'rgba(139, 195, 74, 0.85)',
                    'rgba(205, 220, 57, 0.9)',
                    'rgba(255, 235, 59, 0.95)',
                    'rgba(255, 193, 7, 1)',
                ],

                components: {
                    // Main components
                    preview: true,
                    opacity: true,
                    hue: true,

                    // Input / output Options
                    interaction: {
                        hex: true,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: true,
                        save: true,
                    },
                },
                i18n: comfortsmtp_setting.pickr_i18n,
            });

            pickr
                .on('init', (instance) => {
                    //console.log('Event: "init"', instance);
                })
                .on('hide', (instance) => {
                    //console.log('Event: "hide"', instance);
                })
                .on('show', (color, instance) => {
                    //console.log('Event: "show"', color, instance);
                })
                .on('save', (color, instance) => {
                    //console.log(color.toHEXA().toString());
                    //console.log(color);

                    if (color !== null) {
                        $color_field_fire.data('current-color', color.toHEXA().toString());
                        $color_field.val(color.toHEXA().toString());
                    } else {
                        $color_field_fire.data('current-color', '');
                        $color_field.val('');
                    }

                })
                .on('clear', (instance) => {
                    //console.log('Event: "clear"', instance);
                })
                .on('change', (color, source, instance) => {
                    //console.log('Event: "change"', color, source, instance);
                })
                .on('changestop', (source, instance) => {
                    //console.log('Event: "changestop"', source, instance);
                })
                .on('cancel', (instance) => {
                    //console.log('Event: "cancel"', instance);
                })
                .on('swatchselect', (color, instance) => {
                    //console.log('Event: "swatchselect"', color, instance);
                });
        });

        //select2
        $('.selecttwo-select-wrapper').each(function (index, element) {
            var $element = $(element);

            var $placeholder = $element.data('placeholder');
            var $allow_clear = $element.data('allow-clear');

            $placeholder =
                $placeholder == ''
                    ? comfortsmtp_setting.please_select
                    : $placeholder;

            $element
                .find('.selecttwo-select')
                .select2({
                    placeholder: $placeholder,
                    allowClear: $allow_clear ? true : false,
                    theme: 'default select2-container--cbx',
                    dropdownParent: $(element)
                })
                .on('select2:open', function () {
                    $('.select2-search__field').attr(
                        'placeholder',
                        comfortsmtp_setting?.search
                    );
                })
                .on('select2:close', function () {
                    $('.select2-search__field').attr('placeholder', $placeholder);
                });

            $element
                .find('.select2-selection__rendered')
                .find('.select2-search--inline .select2-search__field')
                .attr('placeholder', $placeholder);
        });

        var $setting_page = $('#comfortsmtp-setting');
        var $setting_nav = $setting_page.find('.setting-tabs-nav');
        var activetab = '';
        if (typeof localStorage !== 'undefined') {
            activetab = localStorage.getItem('comfortsmtpactivetab');

            //if the current active tab doesn't load due to addon deactivation
            if ($(activetab).length === 0) {
                activetab = null;
            }
        }

        //if url has section id as hash then set it as active or override the current local storage value
        if (window.location.hash) {
            if ($(window.location.hash).hasClass('global_setting_group')) {
                activetab = window.location.hash;
                if (typeof localStorage !== 'undefined') {
                    localStorage.setItem('comfortsmtpactivetab', activetab);
                }
            }
        }


        function setting_nav_change($tab_id) {
            if ($tab_id === null) {
                return;
            }

            $tab_id = $tab_id.replace('#', '');

            $setting_nav.find('a').removeClass('active');
            $('#' + $tab_id + '-tab').addClass('active');

            var clicked_group = '#' + $tab_id;

            $('.global_setting_group').hide();
            $(clicked_group).fadeIn();

            //set in local storage
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('comfortsmtpactivetab', clicked_group);
            }

            //load the reset items
            if (clicked_group === '#comfortsmtp_tools') {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: comfortsmtp_setting.ajaxurl,
                    data: {
                        action: 'comfortsmtp_settings_reset_load',
                        security: comfortsmtp_setting.nonce,
                    },
                    success: function (data, textStatus, XMLHttpRequest) {
                        $('#comfortsmtp_resetinfo_wrap').html(data.html);
                    }, //end of success
                }); //end of ajax
            }
        } //end method setting_nav_change

        //click on inidividual nav
        $setting_nav.on('click', 'a', function (e) {
            e.preventDefault();

            var $this = $(this);
            var $tab_id = $this.data('tabid');

            $('.setting-select-nav').val($tab_id);
            $('.setting-select-nav').trigger('change');
        });

        $('.setting-select-nav').on('change', function (e) {
            var $this = $(this);
            var $tab_id = $this.val();

            setting_nav_change($tab_id);
        });

        if (activetab !== null) {
            var activetab_whash = activetab.replace('#', '');

            $('.setting-select-nav').val(activetab_whash);
            $('.setting-select-nav').trigger('change');
        }

        $('.wpsa-browse').on('click', function (event) {
            event.preventDefault();

            var self = $(this);

            // Create the media frame.
            var file_frame = (wp.media.frames.file_frame = wp.media({
                title: self.data('uploader_title'),
                button: {
                    text: self.data('uploader_button_text'),
                },
                multiple: false,
            }));

            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();

                self.prev('.wpsa-url').val(attachment.url);
            });

            // Finally, open the modal
            file_frame.open();
        });

        //make the subheading single row
        $('.setting_heading').each(function (index, element) {
            var $element = $(element);
            var $element_parent = $element.parent('td');

            $element_parent.attr('colspan', 2);
            $element_parent.prev('th').remove();
            $element_parent.parent('tr').removeAttr('class');
            $element_parent.parent('tr').addClass('global_setting_heading_section');
        });

        $('.setting_subheading').each(function (index, element) {
            var $element = $(element);
            var $element_parent = $element.parent('td');

            $element_parent.attr('colspan', 2);
            $element_parent.prev('th').remove();
            $element_parent.parent('tr').removeAttr('class');
            $element_parent
                .parent('tr')
                .addClass('global_setting_subheading_section');
        });

        $('.global_setting_group').each(function (index, element) {
            var $element = $(element);

            $element
                .find('.submit_setting')
                .removeClass('button-primary')
                .addClass('primary');

            var $form_table = $element.find('.form-table');
            $form_table.prev('h2').remove();

            var $i = 0;
            $form_table.find('tr').each(function (index2, element) {
                var $tr = $(element);

                if (!$tr.hasClass('global_setting_heading_section')) {
                    $tr.addClass('global_setting_common_section');
                    $tr.addClass('global_setting_common_section_' + $i);
                } else {
                    $i++;
                    $tr.addClass('global_setting_heading_section_' + $i);
                    $tr.attr('data-counter', $i);
                    $tr.attr('data-is-closed', 0);
                }
            });

            $('#global_setting_group_actions').show();
            $('#global_setting_group_actions').on(
                'click',
                '.global_setting_group_action',
                function (event) {
                    event.preventDefault();

                    $form_table.find('.setting_heading').trigger('click');
                }
            );

            $form_table.on('click', '.setting_heading', function (evt) {
                evt.preventDefault();

                var $this = $(this);
                var $parent = $this.closest('.global_setting_heading_section');
                var $counter = Number($parent.data('counter'));
                var $is_closed = Number($parent.data('is-closed'));

                if ($is_closed === 0) {
                    $parent.data('is-closed', 1);
                    $parent.addClass('global_setting_heading_section_closed');
                    //$('.global_setting_common_section_' + $counter).hide();
                    $('.global_setting_common_section_' + $counter).slideUp();
                } else {
                    $parent.data('is-closed', 0);
                    $parent.removeClass('global_setting_heading_section_closed');
                    //$('.global_setting_common_section_' + $counter).show();
                    $('.global_setting_common_section_' + $counter).slideDown();
                }
            });
        });

        $('.checkbox_fields_check_actions').on(
            'click',
            '.checkbox_fields_check_action_call',
            function (e) {
                e.preventDefault();

                var $this = $(this);
                $this
                    .parent()
                    .next('.checkbox_fields')
                    .find(':checkbox')
                    .prop('checked', true);
            }
        );

        $('.checkbox_fields_check_actions').on(
            'click',
            '.checkbox_fields_check_action_ucall',
            function (e) {
                e.preventDefault();

                var $this = $(this);
                $this
                    .parent()
                    .next('.checkbox_fields')
                    .find(':checkbox')
                    .prop('checked', false);
            }
        );

        //var adjustment_photo;
        $('.checkbox_fields_sortable').sortable({
            vertical: true,
            handle: '.checkbox_field_handle',
            containerSelector: '.checkbox_fields',
            itemSelector: '.checkbox_field',
            placeholder: 'checkbox_field_placeholder',
        });

        /*//var adjustment_photo;
            $('.multicheck_fields_sortable').sortable({
                vertical         : true,
                handle           : '.multicheck_field_handle',
                containerSelector: '.multicheck_fields',
                itemSelector     : '.multicheck_field',
                placeholder      : 'multicheck_field_placeholder'
            });*/

        /*$('.global_setting_group').on('click', '.checkbox', function () {
                var mainParent = $(this).closest('.checkbox-toggle-btn');
                if ($(mainParent).find('input.checkbox').is(':checked')) {
                    $(mainParent).addClass('active');
                } else {
                    $(mainParent).removeClass('active');
                }
            });*/

        //one click save setting for the current tab
        $('#save_settings').on('click', function (e) {
            e.preventDefault();

            var $setting_nav = $('.setting-tabs-nav');

            var $current_tab = $setting_nav.find('.active');
            var $tab_id = $current_tab.data('tabid');
            $('#' + $tab_id)
                .find('.submit_setting')
                .trigger('click');
        });

        $('#setting_info_trig').on('click', function (e) {
            e.preventDefault();

            $('#comfortsmtp_resetinfo').toggle();
        });

        //reset click
        $('#reset_data_trigger').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);

            var notifier = new AWN(comfortsmtp_setting_awn_options);

            var onCancel = () => {
            };

            var onOk = () => {
                $this.hide();
                window.location.href = $this.attr('href');
            };

            notifier.confirm(
                comfortsmtp_setting.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: comfortsmtp_setting.are_you_sure_global,
                    },
                }
            );
        }); //end click #reset_data_trigger

        $('#comfortsmtp_resetinfo_wrap').on(
            'click',
            '.comfortsmtp_setting_options_check_action_call',
            function (e) {
                e.preventDefault();

                var $this = $(this);
                $('#comfortsmtp_resetinfo_wrap').find(':checkbox').prop('checked', true);
            }
        );

        $('#comfortsmtp_resetinfo_wrap').on(
            'click',
            '.comfortsmtp_setting_options_check_action_ucall',
            function (e) {
                e.preventDefault();

                var $this = $(this);
                $('#comfortsmtp_resetinfo_wrap').find(':checkbox').prop('checked', false);
            }
        );

        $('.form-table-fields-parent').sortable({
            vertical: true,
            handle: '.form-table-fields-parent-item-sort',
            itemSelector: '.form-table-fields-parent-item',
            placeholder: 'form-table-fields-parent-item-placeholder'
        });

        //open close the input fields
        $('.form-table-fields-parent').on('click', '.form-table-fields-parent-item-control', function (event) {
            var $this = $(this);

            var $parent = $this.closest('.form-table-fields-parent-item');
            $parent.find('.form-table-fields-parent-item-wrap').toggle();
        });

        //delete the input
        $('.form-table-fields-parent').on('click', '.form-table-fields-parent-item-delete', function (event) {
            var $this = $(this);
            var $parent = $this.closest('.form-table-fields-parent-item');

            var notifier = new AWN(comfortsmtp_setting_awn_options);

            var onCancel = () => {
            };

            var onOk = () => {
                $parent.remove();
            };

            notifier.confirm(
                comfortsmtp_setting.are_you_sure_delete_desc,
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: comfortsmtp_setting.are_you_sure_global,
                    },
                }
            );
        });

        //add new input
        $('.form-table-fields-parent-wrap').on('click', '.form-table-fields-new', function (event) {
            event.preventDefault();

            var $this = $(this);
            var $parent = $this.closest('.form-table-fields-parent-wrap');
            var $items = $parent.find('.form-table-fields-parent');

            var $section_name = $this.data('section_name');
            var $option_name = $this.data('option_name');
            var $field_name = $this.data('field_name');
            var $busy = Number($this.data('busy'));
            var $index = Number($this.data('index'));

            if (!$busy) {
                $this.data('busy', 1);
                $this.addClass('running');
                $this.attr('disabled', true);

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: comfortsmtp_setting.ajaxurl,
                    data: {
                        action: 'comfortsmtp_add_new_field',
                        section_name: $section_name,
                        option_name: $option_name,
                        field_name: $field_name,
                        security: comfortsmtp_setting.nonce,
                        index: $index
                    },
                    success: function (data, textStatus, XMLHttpRequest) {

                        $items.append(data['html']);

                        $this.data('busy', 0);
                        $this.removeClass('running');
                        $this.attr('disabled', false);
                        $this.data('index', Number(data['index']));
                        new AWN(comfortsmtp_setting_awn_options).success(data.message);

                        //$('.cbx-hideshowpassword').hidePassword(true);

                    }
                });
            }
        });
    });
})(jQuery);
//settings
