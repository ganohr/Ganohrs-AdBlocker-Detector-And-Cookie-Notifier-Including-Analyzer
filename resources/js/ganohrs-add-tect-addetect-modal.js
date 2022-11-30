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
jQuery("#gnr-cookie-adb").change(() => {
	Cookies.set('gnr-cookie-adb', 'agreed', {expires: 7});
	hideModal('<?php echo GNR_ACCESSANALYZ_DATA_ADB_AGREE_ADB; ?>');
});
jQuery("#gnr-modal-a").click(() => {
	gnr_adbdc_post('<?php echo GNR_ACCESSANALYZ_DATA_ADB_MOVE; ?>');
});
