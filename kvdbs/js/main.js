var selected = new Array();

function check_select() {
	var name = this.parentNode.id;
	
	if (this.className != 'ico_selected') {
		this.className = 'ico_selected';
		selected.push(name);
	} else {
		this.className = 'ico_select';
		for (i in selected) {
			if (selected[i] == name)
				selected.splice(i, 1);
		}
	}
	update_toolbar();
}

function tb_selectall() {
	var nodes = document.getElementById('list').childNodes;
	
	if ($('.ico_select').length == 0) {
		selected.splice(0, selected.length);
		$('.ico_selected').addClass('ico_select');
		$('.ico_selected').removeClass('ico_selected');
	} else {
		selected.splice(0, selected.length);
		for(var i = 0; i < nodes.length; i++) {
			if(nodes[i].nodeType == 1)
				selected.push(nodes[i].id);
		}
		$('.ico_select').addClass('ico_selected');
		$('.ico_select').removeClass('ico_select');
	}
	update_toolbar();
}

function tb_copy() {
	if (this.className.indexOf('disabled') != -1)
		return;
	show_dialog(260, 280, 'Move To', '<div id="hierarchy">'
		+ '<div class="tree"><ul id="%2F">'
		+ '</ul></div>'
		+ '<div class="panel"><input type="button" value="OK" /><input type="button" value="Cancel" onclick="close_dialog();" /></div>'
		+ '</div>');
	$.post('manage.php?action=list', {'dir' : '/'}, function(data) {
		$('#hierarchy .tree').html(data);
	});
}

function tb_move() {
	if (this.className.indexOf('disabled') != -1)
		return;
	show_dialog(260, 280, 'Move To', '<div id="hierarchy">'
		+ '<div class="tree"><ul id="%2F">'
		+ '</ul></div>'
		+ '<div class="panel"><input type="button" value="OK" /><input type="button" value="Cancel" onclick="close_dialog();" /></div>'
		+ '</div>');
	$.post('manage.php?action=list', {'dir' : '/'}, function(data) {
		$('#hierarchy .tree').html(data);
	});
}

function tb_delete() {
	if (this.className.indexOf('disabled') != -1)
		return;
	$.post('manage.php', {'action' : 'delete', 'dir' : dir, 'name[]' : selected}, function(data){
		location.reload();
	});
}


function tb_share() {
	if (this.className.indexOf('disabled') != -1)
		return;
	$.post('manage.php', {'action' : 'share', 'dir' : dir, 'name[]' : selected}, function(data){
		show_message(380, "Current Sharing", data);
	});
}

function tb_rename() {
	if (this.className.indexOf('disabled') != -1)
		return;
	for (i in selected) {
		var obj = document.getElementById(selected[i]).getElementsByTagName('span')[0];
		obj.innerHTML = '<form class="form_rename" action="manage.php?action=rename" method="POST">' +
			'<input type="hidden" name="name" value="' + selected[i] + '">' +
			'<input type="text" name="value" value="' + selected[i] + '">' +
			'<input type="hidden" name="dir" value="' + dir +'"><input type="submit" value="save">' +
			'<input type="button" filename="' + selected[i] + '" class="cancel" value="cancel"></form>';
	}
	$('input.cancel').click(function() {
		var name =  $(this).attr('filename');
		$(this).parent().remove();
		
		var obj = document.getElementById(name).getElementsByTagName('span')[0];
		obj.innerHTML = '<a href="index.php?name=' + name + '">' + name + '</a>';
	});
}

function check_upload() {
	if($('#upload input[type=file]').val() != '') {
		$('#upload form').submit();
	}
}

function check_newdir() {
	var obj = $('#ipt_newdir');
	
	if (obj.css('visibility') != 'visible') {
		obj.css('visibility', 'visible');
		obj.animate({width: 120}, 'slow');
	} else {
		if (obj.val()) $('#newdir form').submit();
		else {
			obj.animate({width: 2}, 'slow');
			setTimeout(function() {
				obj.css("visibility", "hidden");
			}, 600);
			obj.focus();
		}
	}
}

function update_info() {
	var obj = $('#upload input[type=file]');
	
	if(obj.val() != '') {
		var msg = obj.val()
		var loc = msg.lastIndexOf("\\") + 1;
		if (loc == 0) loc = msg.lastIndexOf("/") + 1;
		msg = msg.substr(loc);
		
		document.getElementById('filename').innerHTML = msg;
	}
}

function update_toolbar() {
	if (selected.length >= 1) {
		$('#tb_copy.disabled').removeClass('disabled');
		$('#tb_move.disabled').removeClass('disabled');
		$('#tb_delete.disabled').removeClass('disabled');
		$('#tb_share.disabled').removeClass('disabled');
		//if (selected.length == 1) {
			$('#tb_rename.disabled').removeClass('disabled');
		//} else {
		//	$('#tb_rename').addClass('disabled');
		//}
	} else {
		$('#tb_copy').addClass('disabled');
		$('#tb_move').addClass('disabled');
		$('#tb_delete').addClass('disabled');
		$('#tb_rename').addClass('disabled');
		$('#tb_share').addClass('disabled');
	}
}

function show_dialog(width, height, title, content) {
	$('#dialog_title').html(title);
	$('#dialog_content').html(content);
	$('#overlay').show();
	$('#dialog').css('margin-left', -(width / 2));
	// $('#dialog').css('margin-top', -(height / 2));	
	if (height != 0)
		$('#dialog').css('height', height);
	$('#dialog').css('width', width).show();
}

function close_dialog() {
	$('#dialog').hide();
	$('#overlay').hide();
	return false;
}

function show_account() {
	show_dialog(280, 240, 'KVDB-MemStorage Account', '<form id="newpwd" method="post" action="account.php?action=newpwd">'
		+ '<li class="clear">Password:<input style="" type="password" name="password"></li>'
		+　'<li class="clear">New:<input style="" type="password" name="newpwd"></li>'
		+　'<li class="clear">Confirm:<input style="" type="password" name="newpwd2"></li>'
		+　'<li class="clear submit"><input type="button" value="Cancel" onclick="close_dialog();"><input type="submit" value="Save"/></li>'
		+　'</form>');
	
	return false;
}

function show_message(width, title, msg, callback) {
	show_dialog(width, 0, title, '<div id="msg"><div id="message" style="margin: 10px;">' + msg
		+ '</div><div class="panel" style="padding-left: ' + ( width - 100 ) * 0.5 + 'px">'
		+ '<input type="button" value="OK" onclick="close_dialog(); location.reload();"></div></div>');
}

function clipboard(id) {
	var text = 'http:/' + host + '/down.php?id=' + id;
	if (window.clipboardData) {
		return (window.clipboardData.setData("Text", text));
	} else if (window.netscape) { 
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect'); 
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard); 
		if (!clip) return; 
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans) return; 
		trans.addDataFlavor('text/unicode'); 
		var str = new Object(); 
		var len = new Object(); 
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString); 
		var copytext = text; 
		str.data = copytext; 
		trans.setTransferData("text/unicode",str,copytext.length*2); 
		var clipid = Components.interfaces.nsIClipboard; 
		if (!clip) return false; 
		clip.setData(trans,null,clipid.kGlobalClipboard); 
		return true; 
   } 
   return false; 
} 


$(document).ready(function() {
	// window.setInterval("update_info()", 500);
	// $('#upload .ico_upload').click(check_upload);
	
	$('#newdir .ico_newdir').click(check_newdir);
	$('.ico_selected, .ico_select').click(check_select);
	$('#tb_selectall').click(tb_selectall);
	$('#tb_copy').click(tb_copy);
	$('#tb_move').click(tb_move);
	$('#tb_delete').click(tb_delete);
	$('#tb_rename').click(tb_rename);
	$('#tb_share').click(tb_share);
	
	$('#upload .ico_upload').click(function() {
		document.getElementById('uploader').submit();
	});
	
	var params = {};
	params.wmode = "transparent";
	params.bgcolor = "#ffffff";
	swfobject.embedSWF("flash/uploader.swf", "uploader", "100", "18", "9.0.0", null, null, params);
});