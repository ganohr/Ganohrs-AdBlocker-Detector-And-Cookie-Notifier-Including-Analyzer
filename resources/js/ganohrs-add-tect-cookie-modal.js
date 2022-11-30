let cookieModal = jQuery('#gnr-cookie-modal');
if (Cookies.get('gnr-cookie-accepted') === undefined) {
	jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_COOKIE_OPEN; ?>'));
	cookieModal.show().delay(2000);
}
jQuery("#gnr-cookie-a").click(() => {
	jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_COOKIE_MOVE; ?>'));
});
jQuery("#gnr-cookie-accept-button").click(() => {
	jQuery.post(access_analizer_url, create_post_data('<?php echo GNR_ACCESSANALYZ_DATA_COOKIE_ACCEPT; ?>'));
	Cookies.set('gnr-cookie-accepted', 'agreed', {expires: 730});
	cookieModal.hide();
});
