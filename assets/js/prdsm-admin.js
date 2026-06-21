/* ==========================================================================
   Admin Accordion UI
   Handles expandable settings sections
========================================================================== */

document.addEventListener( 'DOMContentLoaded', function () {

	// Get all accordion sections.
	const accordionSections = document.querySelectorAll(
		'.prdsm-admin-section'
	);

	accordionSections.forEach( function ( section ) {

		const toggleButton = section.querySelector(
			'.prdsm-admin-section-toggle'
		);

		const content = section.querySelector(
			'.prdsm-admin-section-content'
		);

		const icon = section.querySelector(
			'.prdsm-admin-section-icon'
		);

		if ( ! toggleButton || ! content || ! icon ) {
			return;
		}

		// Open default active accordion.
		if ( section.classList.contains( 'active' ) ) {

			content.style.maxHeight =
				content.scrollHeight + 'px';

			//icon.textContent = '−';

		} else {

			content.style.maxHeight = '0px';

			//icon.textContent = '+';
		}

		// Handle accordion toggle click.
		toggleButton.addEventListener( 'click', function () {

			const isExpanded =
				'true' === toggleButton.getAttribute( 'aria-expanded' );

			// Close all accordion sections first.
			accordionSections.forEach( function ( otherSection ) {

				const otherButton = otherSection.querySelector(
					'.prdsm-admin-section-toggle'
				);

				const otherContent = otherSection.querySelector(
					'.prdsm-admin-section-content'
				);

				const otherIcon = otherSection.querySelector(
					'.prdsm-admin-section-icon'
				);

				if (
					! otherButton ||
					! otherContent ||
					! otherIcon
				) {
					return;
				}

				otherSection.classList.remove( 'active' );

				otherButton.setAttribute(
					'aria-expanded',
					'false'
				);

				otherContent.style.maxHeight = '0px';

				//otherIcon.textContent = '+';
			} );

			// If accordion was previously closed,
			// open the clicked accordion.
			if ( ! isExpanded ) {

				section.classList.add( 'active' );

				toggleButton.setAttribute(
					'aria-expanded',
					'true'
				);

				content.style.maxHeight =
					content.scrollHeight + 'px';

				//icon.textContent = '−';
			}
		} );
	} );

} );