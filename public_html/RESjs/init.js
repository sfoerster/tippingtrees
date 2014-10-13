_5grid.ready(function() {

	// Dropdown Menus (desktop only)
		if (_5grid.isDesktop)
			$('#nav > ul').dropotron({
				offsetY: -20,
				offsetX: -1,
				mode: 'fade',
				noOpenerFade: true
			});

	// Banner slider
		var banner = $('#slider');
		if (banner.length > 0)
		{
			banner.slidertron({
				mode: 'fade',	// Change this to 'slide' to switch back to sliding mode
				viewerSelector: '.viewer',
				reelSelector: '.viewer .reel',
				slidesSelector: '.viewer .reel .slide',
				slideLinkSelector: '.link',
				indicatorSelector: '.indicator ul li',
				advanceDelay: 5000,
				speed: 600,
				autoFit: true,
				autoFitAspectRatio: (782 / 379),
				seamlessWrap: false
			});

			if (_5grid.isMobile)
			{
				_5grid.orientationChange(function() {
					banner.trigger('slidertron_reFit');
				});

				_5grid.mobileUINavOpen(function() {
					banner.trigger('slidertron_stopAdvance');
				});
			}
		}

});