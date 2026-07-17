document.addEventListener("DOMContentLoaded", function () {

	const overlay = document.getElementById("prdsm-overlay");
	const menu    = document.getElementById("prdsm-slide-menu");
	const header = document.querySelector("#main-header, .et-l--header, header");

	if (!overlay || !menu) return;

	// -------------------------------------
	// Translation Labels (with fallback)
	// -------------------------------------

	const openLabel  = (typeof prdsmSettings !== "undefined" && prdsmSettings.openLabel)
		? prdsmSettings.openLabel
		: "Open Menu";

	const closeLabel = (typeof prdsmSettings !== "undefined" && prdsmSettings.closeLabel)
		? prdsmSettings.closeLabel
		: "Close Menu";

	// -------------------------------------
	// Create Hamburger Button
	// -------------------------------------

	const hamburger = document.createElement("button");
	hamburger.id = "prdsm-hamburger";

	/**
	 * Add selected hamburger style class.
	 */
	if (
		typeof prdsmSettings !== "undefined" &&
		prdsmSettings.hamburgerStyle
	) {
		hamburger.classList.add(
			'prdsm-hamburger-style-' +
			prdsmSettings.hamburgerStyle
		);
	}

	/**
	 * Add slide direction class to hamburger trigger.
	 */
	hamburger.classList.add(
		"prdsm-direction-" + prdsmSettings.slideDirection
	);
	hamburger.setAttribute("type", "button");
	hamburger.setAttribute("aria-label", openLabel);
	hamburger.setAttribute("aria-expanded", "false");
	hamburger.setAttribute("aria-controls", "prdsm-slide-menu");
	hamburger.innerHTML = "<span></span><span></span><span></span>";

	// Apply color from settings
	if (typeof prdsmSettings !== "undefined" && prdsmSettings.hamburgerColor) {
		const spans = hamburger.querySelectorAll("span");
		spans.forEach(function (span) {
			span.style.background = prdsmSettings.hamburgerColor;
		});
	}

	// Store colors safely
	const hamburgerColor = (typeof prdsmSettings !== "undefined" && prdsmSettings.hamburgerColor) ? prdsmSettings.hamburgerColor : null;
	const closeIconColor = (typeof prdsmSettings !== "undefined" && prdsmSettings.closeIconColor) ? prdsmSettings.closeIconColor : null;

	document.body.appendChild(hamburger);

	// -------------------------------------
	// Position Hamburger
	// -------------------------------------

	function getMeasurableHeader( headerEl ) {

		// Divi Theme Builder headers can collapse the outer wrapper to 0
		// height (e.g. transparent headers using position: absolute).
		// The section carrying a "_tb_header" class is Divi's own marker
		// for the actual header content and reliably has real height.
		const tbHeaderSection = headerEl.querySelector( '[class*="_tb_header"]' );

		const candidates = [
			tbHeaderSection,
			headerEl.querySelector( '.et_builder_inner_content' ),
			headerEl
		];

		for ( const el of candidates ) {
			if ( el && el.getBoundingClientRect().height > 0 ) {
				return el;
			}
		}

		return headerEl;
	}

	function positionHamburger() {

		if ( ! header ) {
			return;
		}

		/**
		 * Detect active Divi sticky header state.
		 */
		const stickyActive = document.querySelector(
			'.et_pb_sticky'
		);

		/**
		 * When sticky header is active,
		 * lock hamburger to fixed top position.
		 */
		if ( stickyActive ) {

			/**
			 * Get active sticky header dimensions.
			 */
			const stickyRect =
				stickyActive.getBoundingClientRect();
				
			const hamburgerHeight =
				hamburger.offsetHeight;
				
			/**
			 * Vertically center hamburger
			 * inside sticky header.
			 */
			const stickyTopPosition =
				stickyRect.top +
				( stickyRect.height / 2 ) -
				( hamburgerHeight / 2 );
				
			hamburger.style.top =
				stickyTopPosition + 'px';
				
			return;
		}

		/**
		 * Normal non-sticky positioning.
		 */
		const measurableHeader = getMeasurableHeader( header );
		const headerRect = measurableHeader.getBoundingClientRect();

		const hamburgerHeight = hamburger.offsetHeight;

		const topPosition =
			headerRect.top +
			( headerRect.height / 2 ) -
			( hamburgerHeight / 2 );

		hamburger.style.top = topPosition + 'px';
	}

	positionHamburger();
	window.addEventListener("resize", positionHamburger);

	let ticking = false;
	window.addEventListener("scroll", function () {
		if (!ticking) {
			window.requestAnimationFrame(function () {
				positionHamburger();
				ticking = false;
			});
			ticking = true;
		}
	});

	function setHamburgerColor(color) {
		if (!color) return;
		const spans = hamburger.querySelectorAll("span");
		spans.forEach(function (span) {
			span.style.background = color;
		});
	}

	// -------------------------------------
	// Accessibility Base State
	// -------------------------------------

	menu.setAttribute("aria-hidden", "true");

	// -------------------------------------
	// Open / Close Functions
	// -------------------------------------

	function openMenu() {
		overlay.classList.add("active");
		menu.classList.add("active");
		hamburger.classList.add("active");

		document.documentElement.classList.add("prdsm-lock");
		document.body.classList.add("prdsm-lock");

		hamburger.setAttribute("aria-expanded", "true");
		hamburger.setAttribute("aria-label", closeLabel);
		menu.setAttribute("aria-hidden", "false");

		const firstLink = menu.querySelector("a");
		if (firstLink) firstLink.focus();

		if (closeIconColor) {
			setHamburgerColor(closeIconColor);
		}
	}

	function closeMenu() {
		overlay.classList.remove("active");
		menu.classList.remove("active");
		hamburger.classList.remove("active");

		document.documentElement.classList.remove("prdsm-lock");
		document.body.classList.remove("prdsm-lock");

		hamburger.setAttribute("aria-expanded", "false");
		hamburger.setAttribute("aria-label", openLabel);
		menu.setAttribute("aria-hidden", "true");

		hamburger.focus();

		if (hamburgerColor) {
			setHamburgerColor(hamburgerColor);
		}
	}

	// -------------------------------------
	// Events
	// -------------------------------------

	hamburger.addEventListener("click", function () {
		if (menu.classList.contains("active")) {
			closeMenu();
		} else {
			openMenu();
		}
	});

	overlay.addEventListener("click", closeMenu);

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape" && menu.classList.contains("active")) {
			closeMenu();
		}
	});

	const menuLinks = menu.querySelectorAll( 'a' );

	menuLinks.forEach( function( link ) {
	
		link.addEventListener( 'click', function() {
		
			closeMenu();
		
		} );
	
	} );

	// -------------------------------------
	// Dropdown Handling
	// -------------------------------------

	const dropdownParents = menu.querySelectorAll(".menu-item-has-children");

	dropdownParents.forEach(function (parentItem, index) {

		const parentLink = parentItem.querySelector("a");
		const submenu = parentItem.querySelector(".sub-menu");

		if (!parentLink || !submenu) return;

		const submenuId = "prdsm-submenu-" + index;
		submenu.id = submenuId;

		const parentRow = document.createElement("div");
		parentRow.classList.add("prdsm-parent-row");

		parentRow.appendChild(parentLink);

		const toggleBtn = document.createElement("button");
		toggleBtn.classList.add("prdsm-sub-toggle");
		toggleBtn.setAttribute("type", "button");
		toggleBtn.setAttribute("aria-expanded", "false");
		toggleBtn.setAttribute("aria-controls", submenuId);
		toggleBtn.innerHTML = "+";

		parentRow.appendChild(toggleBtn);
		parentItem.insertBefore(parentRow, submenu);

		submenu.classList.remove("active");

		toggleBtn.addEventListener("click", function (e) {
			e.preventDefault();
			e.stopPropagation();

			const isOpen = submenu.classList.contains("active");

			if (isOpen) {

				submenu.style.maxHeight = submenu.scrollHeight + "px";

				requestAnimationFrame(() => {
					submenu.style.maxHeight = "0px";
				});

				submenu.classList.remove("active");
				toggleBtn.classList.remove("active");
				toggleBtn.setAttribute("aria-expanded", "false");

				submenu.addEventListener("transitionend", function () {
					submenu.style.maxHeight = null;
				}, { once: true });

			} else {

				submenu.classList.add("active");
				submenu.style.maxHeight = submenu.scrollHeight + "px";

				toggleBtn.classList.add("active");
				toggleBtn.setAttribute("aria-expanded", "true");
			}
		});

		toggleBtn.addEventListener("keydown", function (e) {
			if (e.key === "Enter" || e.key === " ") {
				e.preventDefault();
				toggleBtn.click();
			}
		});

	});

});