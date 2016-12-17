<script>
	var mySwiper = new Swiper('.swiper-container', {
    	speed: 400,
    	spaceBetween: 50,
    	scrollbar: ".swiper-scrollbar",
    	nextButton: ".swiper-button-next",
    	prevButton: ".swiper-button-prev",
    	pagination: '.swiper-pagination',
        paginationClickable: true,
        preloadImage: false,
        lazyLoading: true
	});
	
	function swiperUpdate() {
		$$('.popup-swiper').on('open', function () {
		var mySwiper = $$('.swiper-container')[0].swiper;
		mySwiper.update();
		});
	}
	
	mySwiper.update();
	
</script>