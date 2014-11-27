jQuery(document).ready(function ($) {

    //Datepicker and daterange
    $('.daterange.datepicker').each(function() {
        var thisDatePicker = $(this);
        thisDatePicker.datepicker({ minDate: thisDatePicker.data("mindate"), maxDate: thisDatePicker.data("maxdate"), dateFormat: GformsDateLimiterParams.dateFormat });
    });
    
});