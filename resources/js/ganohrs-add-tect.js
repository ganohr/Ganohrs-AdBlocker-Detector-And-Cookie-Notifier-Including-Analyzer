let total  = 0;
let errors = 0;
let allowOuterModalClick = false;

function myJqueryFunctions() {
	const modal = jQuery("#gnr-modal");
	const hideModal = data => {
		allowOuterModalClick = false;
		jQuery.post(access_analizer_url, create_post_data(data));
		modal.fadeOut();
		setTimeout(() => jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_ADB_CONTINUE_VIEWING; ?>')), 10000);
	};
	const showModal = () => {
		if (Cookies.get('gnr-cookie-adb') === undefined) {
			jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_ADB_OPEN; ?>'));
			allowOuterModalClick = true;
			modal.show().delay(2000);
		} else {
			allowOuterModalClick = false;
			jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_ADB_SKIP; ?>'));
		}
	};
	setTimeout(() => {
		jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_CONTINUE_VIEWING; ?>'));
	}, 10000);
	setTimeout(() => {
		jQuery('#gnr-modal-close').click(() => {
			hideModal('<?php echo GNR_ACCESSANALYZ_DATA_ADB_CLOSE_BUTTON; ?>');
		});
		jQuery(document).click(e => {
			if (   allowOuterModalClick === true
				&& !jQuery(e.target).closest('#gnr-modal-content').length
				&& !jQuery(e.target).closest('#gnr-modal-close'  ).length
				&& !jQuery(e.target).closest('#gnr-cookie-modal' ).length
				&& !jQuery(e.target).closest('#gnr-cookie-accept-button' ).length
			){
				hideModal('<?php echo GNR_ACCESSANALYZ_DATA_ADB_CLOSE_OUTER; ?>');
			}
		});
		const elements = document.querySelectorAll(".ad-space, .adWidget, .adsbygoogle");
		if (elements === null || elements.length === 0) {
			showModal();
		} else {
			elements.forEach(element => {
				total++;
				if(element.clientHeight === 0) {
					errors++;
				}
			});
			setTimeout(() => {
				if(total * 0.5 < errors) {
					showModal();
				}
			}, 1000);
		}
	}, 20000);
}
document.querySelectorAll("img[src*='//ws-fe.amazon']").forEach(
	img => {
		total += 0.1;
		img.onerror = () => errors++;
		img.src = img.src;
	}
);
