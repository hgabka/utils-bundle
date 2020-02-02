var datePicker = {};
import moment from 'moment-with-locales-es6';
window.moment = moment;
var datepicker = require('bootstrap-datepicker');
require ('bootstrap-datepicker/dist/locales/bootstrap-datepicker.hu.min.js');
require('./bootstrap-datetimepicker.min.js');

datePicker.datepicker = (function($, window, undefined) {

    var init, reInit, _setDefaultDate, _initDatepicker;

    var _today = window.moment(),
        _tomorrow = window.moment(_today).add(1, 'days');

    var defaultFormat = 'DD-MM-YYYY',
        defaultCollapse = true,
        defaultKeepOpen = false,
        defaultMinDate = false,
        defaultShowDefaultDate = false,
        defaultLocale = 'hu',
        defaultStepping = 1,
        defaultWithTime = true,
        defaultWithDate = true;


    init = function() {
        $('.js-datepicker').each(function() {
            _initDatepicker($(this));
        });
    };

    reInit = function(el) {
        if (el) {
            _initDatepicker($(el));
        } else {
            $('.js-datepicker').each(function() {
                if (!$(this).hasClass('datepicker--enabled')) {
                    _initDatepicker($(this));
                }
            });
        }
    };

    _setDefaultDate = function(elMinDate) {
        if(elMinDate === 'tomorrow') {
            return _tomorrow;
        } else {
            return _today;
        }
    };


    _initDatepicker = function($el) {
        // Get Settings
        var elFormat = $el.data('format'),
            elCollapse = $el.data('collapse'),
            elKeepOpen = $el.data('keep-open'),
            elMinDate = $el.data('min-date'),
            elShowDefaultDate = $el.data('default-date'),
            elLocale = $el.data('locale'),
            elOptions = $el.data('options'),
            elStepping = $el.data('stepping'),
        elWithDate = $el.data('with-date'),
        elWithTime = $el.data('with-time');


        // Set Settings
        var format = (elFormat !== undefined) ? elFormat : defaultFormat,
            collapse = (elCollapse !== undefined) ? elCollapse : defaultCollapse,
            keepOpen = (elKeepOpen !== undefined) ? elKeepOpen : defaultKeepOpen,
            minDate = (elMinDate === 'tomorrow') ? _tomorrow : (elMinDate === 'today') ? _today : defaultMinDate,
            locale = (elLocale !== undefined) ? elLocale : defaultLocale,
            options = (elOptions !== undefined) ? elOptions : {},
            defaultDate = (elShowDefaultDate) ? _setDefaultDate(elMinDate) : defaultShowDefaultDate,
            stepping = (elStepping !== undefined) ? elStepping : defaultStepping,
            withTime = (elWithTime !== undefined) ? elWithTime : defaultWithTime,
            withDate = (elWithDate !== undefined) ? elWithDate : defaultWithDate;

        // Setup
        var $input = $el.find('input'),
            $addon = $el.find('.input-group-addon');

        var defOptions = {
            format: format,
            collapse: collapse,
            keepOpen: keepOpen,
            minDate: minDate,
            defaultDate: defaultDate,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'auto'
            },
            widgetParent: $el,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-arrow-left',
                next: 'fa fa-arrow-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash-o'
            },
            stepping: stepping,
            language: locale,
            pickTime: withTime,
            pickDate: withDate
        };

        options = $.extend(defOptions, options);
        $input.datetimepicker(options);

        $el.addClass('datepicker--enabled');

        $addon.on('click', function() {
            $input.focus();
        });
    };


    return {
        init: init,
        reInit: reInit
    };

})(jQuery, window);

export default datePicker;