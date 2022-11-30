let total  = 0;
let errors = 0;
let allowOuterModalClick = false;

const access_analizer_url = 'https://ganohr.net/gnr-access-analyze/gnr-access-analizer.php';

function gnr_adbdc_post() {
	jQuery.post(
	    access_analizer_url,
	    {
    		data: data,
    	},
	);
}
