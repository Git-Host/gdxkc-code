var file_name = "";
var file_size = 0;

function uploader_url() {
	return location.href.substr(0, location.href.indexOf('home.php')) + 'upload.php';;
}

function uploader_dir() {
	return dir;
}

function uploader_ready() {
	$('#filename').html('<span>Browse</span>');
}

function uploader_selected(file) {
	file_name = file[0];
	file_size = file[1];
	$('#filename').html('<span>Loading</span>');
}

function uploader_loaded() {
	$('#filename').html('<span>Prepared</span>');
}

function uploader_begin() {
	$('#filename').html('<span>Starting</span>');
}

function uploader_progress(percent) {
	if (file_size > 8 * 1024 * 1024)
		$('#filename').html('<div style="width: ' + percent + 'px; height: 18px; background: #a3a8e7;">&nbsp;&nbsp;&nbsp;' + percent + '&nbsp;%</div>');
	else
		$('#filename').html('<span>Uploading</span>');
}

function uploader_finish() {
	//alert('finish');
	location.reload();
}

function uploader_error(msg) {
	alert(msg);
}