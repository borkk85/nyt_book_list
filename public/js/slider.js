$(document).ready(function(){
    $('.category-nav ul').slick({
        infinite: false,
        slidesToShow: 5, /* Number of items you want to see on the screen */
        slidesToScroll: 5, /* Number of items to scroll at once */
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});

// jQuery(document).ready(function($) {
// 	console.log('initialized');
//     $('.popup-checkbox').hide();

//     $('#wpforms-2533-field_16').change(function() {
//         if ($(this).val() === "Perspectiefvol leiderschap. Management in de 21e eeuw.") {
//             $('.popup-checkbox').show();
//         } else {
//             $('.popup-checkbox').hide();
//         }
//     });
// });