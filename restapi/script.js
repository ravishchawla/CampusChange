getAllItems = function() {
	var path = 'api/listings/';
	var sessionID = document.getElementById('rootSessionID').value;
	
	var response = sendGetRequest(path, sessionID, false);

	var selector = document.getElementById('itemsSelector');
	/*response = {
		total_rows: 1,
		rows: [
			{
				id: '0c04b7c812e64aaf988ffd706f005228'
			}]
	}
*/
	for(var i = 0; i < response.length; i++) {
		var opt = document.createElement("option");
		opt.value = i;
		opt.innerHTML = response[i].id;
		selector.appendChild(opt);
	}
	console.log(response);
	

}

getItem = function() {
	var path='api/listings/';
	var sessionID = document.getElementById('rootSessionID').value;
	var selector = document.getElementById('itemsSelector');
	var id = selector.options[selector.selectedIndex].innerHTML;
	path = path.concat(id);
	var response = sendGetRequest(path, sessionID, false);
	console.log(response);
}

sendGetRequest = function(path, sessionID, _async) {
	var response = null;
	$.ajax({
		type: "GET", 
		url: path,
		async: _async,
		headers: {"X-SessionId" : sessionID},
		success: function(res) {
			response = res;
		},
		error: function(xhr, erro, r) {
			console.log(xhr);
		}
	});
	return response;
}

authUser = function(_email, _password) {
	var path = 'api/auth/';
	var data = {
				email: _email,
				password: _password
	}
	var sessionID = null;
	sendPostRequest(path, data, sessionID);
}

insertUser = function(_email, _fname, _lname, _password) {
	var path = 'api/user/';
	var data = {
				emaiL: _email,
				fname: _fname,
				lname: _lname,
				password: _password
	}

	sendPostRequest(path, data, null);
}

addItem = function() {
	var _name = document.getElementById('itemAddName').value;
	var _askingPrice = document.getElementById('itemAddAskingPrice').value;
	var _category = document.getElementById('itemAddCategory').value;
	var _description = document.getElementById('itemAddDescription').value;
	var _token = document.getElementById('itemAddToken').value;
	var _path = 'api/listings/';

	var data = {
		name: _name,
		askingPrice: _askingPrice,
		category: _category,
		description: _description,
	}
	sendPostRequest(_path, data, _token);
}

updateItem = function() {
	var selector = document.getElementById('itemsSelector');
	var _askingPrice = document.getElementById('itemUpAskingPrice').value;
	var _category = document.getElementById('itemUpCategory').value;
	var _description = document.getElementById('itemUpDescription').value;
	var _token = document.getElementById('itemUpToken').value;
	var _path = 'api/listings/';
	var id = selector.options[selector.selectedIndex].innerHTML;
	_path = _path.concat(id);
	
	var data = {
		askingPrice: _askingPrice,
		category: _category,
		description: _description,
	}
	sendPutRequest(_path, data, _token);
}

sendPostRequest = function(path, _data, sessionID) {
	if(sessionID != null){
		$.ajax({
			type: "POST",
			url: path,
			headers: {"X-SessionId": sessionID},
			data: _data,
			success: function(response) {
				console.log(response);
			},
			error: function(xhr, erro, r) {
				console.log(xhr);

			}
		});
	}
	else {
		$.ajax({
			type: "POST",
			url: path,
			data: _data,
			success: function(response) {
				console.log(response);
			},
			error: function(xhr, erro, r) {
				console.log(xhr);

			}
		});
	}
}

deleteUser = function(pass, sessionID) {
	var path = 'api/user';
	var data = {
				password: pass
	}
	sendDeleteRequest(path, data, sessionID);
}

deleteItem = function() {
	var selector = document.getElementById('itemsSelector');
	var id = selector.options[selector.selectedIndex].innerHTML;
	var path = 'api/listings/';
	path = path.concat(id);
	var _token = document.getElementById('itemUpToken').value;
	sendDeleteRequest(path, null, _token);

}

sendDeleteRequest = function(path, _data, sessionID) {
	if(_data == null) {
	$.ajax({
		type: "DELETE",
		url: path,
		headers: {"X-SessionId": sessionID},
		success: function(response) {
			console.log(response);
		},
		error: function(xhr, erro, r) {
			console.log(xhr);
		}
	});
}
else {
	$.ajax({
		type: "DELETE",
		url: path,
		data: _data,
		headers: {"X-SessionId": sessionID},
		success: function(response) {
			console.log(response);
		},
		error: function(xhr, erro, r) {
			console.log(xhr);
		}
	});
}
}

updateUser = function(oldPass, newPass, sessionID) {
	var path = 'api/user';
	var data = {
		oldPassword: oldPass,
		newPassword: newPass
	}
	sendPutRequest(path, data, sessionID);
}

sendPutRequest = function(path, _data, sessionID) {
	$.ajax({
			type: "PUT",
			url: path,
			data: _data,
			headers: {"X-SessionId": sessionID},
			success: function(response) {
				console.log(response);
			},
			error: function(xhr, erro, r) {
				console.log(xhr);

			}
		});
}