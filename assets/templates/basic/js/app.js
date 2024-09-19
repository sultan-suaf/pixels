(function ($) {
	"use strict";

	// Screen Size Counting
	let screenSize = window.innerWidth;
	window.addEventListener("resize", function (e) {
		screenSize = window.innerWidth;
	});

	$(document).ready(function () {
		// Search Popup
		var bodyOvrelay = $("#body-overlay");
		var searchPopup = $("#search-popup");

		$(document).on("click", "#body-overlay", function (e) {
			e.preventDefault();
			bodyOvrelay.removeClass("active");
			searchPopup.removeClass("active");
		});
		$(document).on("click", ".search--toggler", function (e) {
			e.preventDefault();
			searchPopup.addClass("active");
			bodyOvrelay.addClass("active");
		});
		// Search Popup End

		// Animate the scroll to top
		$(".back-to-top").on("click", function (event) {
			event.preventDefault();
			$("html, body").animate({
				scrollTop: 0
			}, 300);
		});


		$('.menu-toggle').on('click', function () {
			$('.overlay').toggleClass('active');
		});
		$('.overlay').on('click', function () {
			$('.overlay').removeClass('active');
			$(".navbar-toggler").click();
		});

		// Mobile Submenu
		let primaryMenuLink = $(".primary-menu__link");
		let primaryMenu = $(".has-sub > .primary-menu__link");
		let primarySubMenu = $(".primary-menu__sub");
		if (primaryMenu || primarySubMenu || primaryMenuLink) {
			primaryMenu.on("click", function (e) {
				e.preventDefault();
				if (parseInt(screenSize) < parseInt(992)) {
					$(this).toggleClass("active").siblings(primarySubMenu).slideToggle();
				}
				if (parseInt(screenSize) >= parseInt(992)) {
					e.stopPropagation();
					$(this)
						.toggleClass("active")
						.parent()
						.siblings()
						.children(".primary-menu__link")
						.removeClass("active");
				}
			});
			primarySubMenu.each(function () {
				if (parseInt(screenSize) >= parseInt(992)) {
					$(this).on("click", function (e) {
						e.stopPropagation();
					});
				}
			});
			$(document).on("click", function () {
				if (parseInt(screenSize) >= parseInt(992)) {
					primaryMenuLink.removeClass("active");
				}
			});
		}

		// mega menu js 




		// Mobile Submenu End

		// Custom Dropdown
		let customDropdown = $('[data-set="custom-dropdown"]');
		let dropdownContent = $(".custom-dropdown__content");
		if (customDropdown || dropdownContent) {
			customDropdown.each(function () {
				$(this).on("click", function (e) {
					e.stopPropagation();
					$("body").toggleClass("custom-dropdown-open");
					dropdownContent.toggleClass("is-open");
				});
			});
			dropdownContent.each(function () {
				$(this).on("click", function (e) {
					e.stopPropagation();
				});
			});
			$(document).on("click", function () {
				$("body").removeClass("custom-dropdown-open");
				dropdownContent.removeClass("is-open");
			});
		}
		// Custom Dropdown End

		// Category Slider
		let categorySlider = $(".category__slider");
		if (categorySlider) {
			categorySlider.slick({
				mobileFirst: true,
				slidesToShow: 2,
				prevArrow: '<button type="button" class="category__slider-arrow category__slider-prev"><i class="las la-angle-left"></i></button>',
				nextArrow: '<button type="button" class="category__slider-arrow category__slider-next"><i class="las la-angle-right"></i></button>',
				responsive: [{
						breakpoint: 500,
						settings: {
							slidesToShow: 3,
						},
					},
					{
						breakpoint: 767,
						settings: {
							slidesToShow: 4,
						},
					},
					{
						breakpoint: 991,
						settings: {
							slidesToShow: 5,
						},
					},
					{
						breakpoint: 1199,
						settings: {
							slidesToShow: 7,
						},
					},
					{
						breakpoint: 1399,
						settings: {
							slidesToShow: 8,
						},
					},
					{
						breakpoint: 1599,
						settings: {
							slidesToShow: 9,
						},
					},
					{
						breakpoint: 1919,
						settings: {
							slidesToShow: 11,
						},
					},
				],
			});
		}
		// Category Slider End

		// Search Category Slider
		let searchCategory = $(".search-category");
		if (searchCategory) {
			searchCategory.slick({
				mobileFirst: true,
				slidesToShow: 2,
				prevArrow: '<button type="button" class="search-category__arrow search-category__arrow-prev"><i class="las la-angle-left"></i></button>',
				nextArrow: '<button type="button" class="search-category__arrow search-category__arrow-next"><i class="las la-angle-right"></i></button>',
				responsive: [{
						breakpoint: 374,
						settings: {
							slidesToShow: 3,
						},
					},
					{
						breakpoint: 575,
						settings: {
							slidesToShow: 4,
						},
					},
					{
						breakpoint: 767,
						settings: {
							slidesToShow: 5,
						},
					},
					{
						breakpoint: 991,
						settings: {
							slidesToShow: 6,
						},
					},
					{
						breakpoint: 1399,
						settings: {
							slidesToShow: 7,
						},
					},
					{
						breakpoint: 1599,
						settings: {
							slidesToShow: 8,
						},
					},
					{
						breakpoint: 1919,
						settings: {
							slidesToShow: 10,
						},
					},
				],
			});
			searchCategory.on(
				"beforeChange",
				function (event, slick, currentSlide, nextSlide) {
					event.target.classList.add("active");
					if (nextSlide === 0) {
						event.target.classList.remove("active");
					}
				}
			);
		}
		// Search Category Slider End

		// Tooltip Initalize
		const tooltipTriggerList = document.querySelectorAll(
			'[data-bs-toggle="tooltip"]'
		);
		const tooltipList = [...tooltipTriggerList].map(
			(tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
		);
		// Tooltip Initalize End


		// Filter Toggle
		let filterToggle = $(".primary-search__filter");
		let filterBar = $(".search-page__filter");
		let filterClose = $(".filter-close");
		let filterBackdrop = $(".filter-sidebar--backdrop");
		if (filterToggle && filterBar) {
			filterToggle.on("click", function () {
				filterBar.toggleClass("show");
			});
		}
		if (filterClose || filterBar || filterBackdrop) {
			filterClose.on("click", function () {
				filterBar.toggleClass("show");
			});
			filterBackdrop.on("click", function () {
				filterBar.removeClass("show");
			});
		}
		// Filter Toggle End

		// Password Show Hide Toggle
		let passTypeToggle = $(".pass-toggle");
		if (passTypeToggle) {
			passTypeToggle.each(function () {
				$(this).on("click", function () {
					$(this)
						.children()
						.toggleClass("fas fa-eye-slash")
						.toggleClass("fas fa-eye");
					var input = $(this).parent().find("input");
					if (input.attr("type") == "password") {
						input.attr("type", "text");
					} else {
						input.attr("type", "password");
					}
				});
			});
		}
		// Password Show Hide Toggle End
		// user Dashboard Menu Toggle
		let userMenuToggle = $(".dashboard-sidebar__nav-toggle-btn");
		let userMenuClose = $(".dashboard-menu__head-close");
		if (userMenuToggle || userMenuClose) {
			userMenuToggle.on("click", function () {
				$("body").toggleClass("dashboard-menu-open");
			});
			userMenuClose.on("click", function () {
				$("body").toggleClass("dashboard-menu-open");
			});
		}
		// user Dashboard Menu Toggle End


		//opened active menu
		let activeMenu = $(".dashboard-menu__inner-link.active");
		if (activeMenu) {
			let parentDiv = activeMenu.parents(".accordion");
			parentDiv.find(".accordion-button").attr("aria-expanded", true);
			parentDiv.find(".accordion-collapse").addClass("show");
		}
	});

	// add custom--modal class
	let modal = $("#confirmationModal");
	modal.addClass("custom--modal");
	modal.find(".close").addClass("btn-close").text("");
	modal.find(".modal-footer").find("button").addClass("sm-text");


	$(window).on("scroll", function () {
		var ScrollTop = $(".back-to-top");
		if ($(window).scrollTop() > 1200) {
			ScrollTop.fadeIn(1000);
		} else {
			ScrollTop.fadeOut(1000);
		}
	});

	$(window).on("load", function () {
		// Preloader
		var preLoder = $(".preloader");
		preLoder.fadeOut(1000);
	});



	Array.from(document.querySelectorAll("table")).forEach((table) => {
		let heading = table.querySelectorAll("thead tr th");
		Array.from(table.querySelectorAll("tbody tr")).forEach((row) => {
			Array.from(row.querySelectorAll("td")).forEach((colum, i) => {
				colum.setAttribute("data-label", heading[i].innerText);
			});
		});
	});

	//lazy loading image
	let images = document.querySelectorAll(".lazy-loading-img");

	function preloadImage(image) {
		const src = image.getAttribute("data-image_src");
		image.src = src;
	}

	let imageOptions = {
		threshold: 0.3,
	};

	const imageObserver = new IntersectionObserver((entries, imageObserver) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			} else {
				preloadImage(entry.target);
				imageObserver.unobserve(entry.target);
			}
		});
	}, imageOptions);

	images.forEach((image) => {
		imageObserver.observe(image);
	});

	//lazy loading image end

	$(".showFilterBtn").on("click", function () {
		$(".responsive-filter-card").toggleClass("show-filter");
	});

	$(".custom--modal").on("shown.bs.modal", function () {
		let main = $(".for-blur");
		main.addClass("active");
	});

	$(".custom--modal").on("hidden.bs.modal", function () {
		let main = $(".for-blur");
		main.removeClass("active");
	});

	$.each($("input, select, textarea"), function (i, element) {
		if (element.hasAttribute("required")) {
			$(element).siblings("label").addClass("required");
		}
	});

	$("#confirmationModal").find(".btn--primary").addClass("btn--base").removeClass("btn--primary");


	let elements = document.querySelectorAll('[s-break]');
	Array.from(elements).forEach(element => {
		let html = element.innerHTML;

		if (typeof html != 'string') {
			return false;
		}
		let breakLength = parseInt(element.getAttribute('s-break'));
		html = html.split(" ");
		let colorText = [];

		if (breakLength < 0) {
			colorText = html.slice(breakLength);
		} else {
			colorText = html.slice(0, breakLength);
		}

		let solidText = [];

		html.filter(ele => {
			if (!colorText.includes(ele)) {
				solidText.push(ele);
			}
		});

		colorText = `<span class="text--base">${colorText.toString().replaceAll(',', ' ')}</span>`;
		solidText = solidText.toString().replaceAll(',', ' ');

		breakLength < 0 ? element.innerHTML = `${solidText} ${colorText}` : element.innerHTML = `${colorText} ${solidText}`;
	});
})(jQuery);
