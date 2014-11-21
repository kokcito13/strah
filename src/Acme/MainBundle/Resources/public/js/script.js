$(document).ready(function() {
	
	$(".search-block .search-fild").focus(function(){
		$(this).closest('.search-block').addClass('active');
	}).focusout(function(){
		$(this).closest('.search-block').removeClass('active');
	});

	jQuery('.jcarousel').jcarousel({
		wrap: 'circular'
	})
	.jcarouselAutoscroll({
        interval: 5000,
        target: '+=3',
        autostart: true
    });
	jQuery('.jcarousel-pagination')
		.on('jcarouselpagination:active', 'a', function() {
			$(this).addClass('active');
		})
		.on('jcarouselpagination:inactive', 'a', function() {
			$(this).removeClass('active');
		})
		.jcarouselPagination();
    $('body a.arrow-top').click(function(e){
        e.preventDefault();
        $('html,body').animate({ scrollTop: 0 }, 'slow');
    });

    $('a.some_way').click(function(e){
        e.preventDefault();

        var uri = $(this).data('url');
        setTimeout(function(){
            window.open(uri, '_blank');
        }, 200);
    });
});