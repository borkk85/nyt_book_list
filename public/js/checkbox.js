
jQuery(document).ready(function($) {
	console.log('initialized');
    $('.popup-checkbox').hide();

    $('#wpforms-2533-field_16').change(function() {
        if ($(this).val() === "Perspectiefvol leiderschap. Management in de 21e eeuw.") {
            $('.popup-checkbox').show();
        } else {
            $('.popup-checkbox').hide();
        }
    });
});