app.factory('client', function($http, $q, model) {
	function signIn(email, password) {
		var deferred = $q.defer();
		
		$http.get('/thrift.php', {
			params: {
				action: 'auth',
				user: email,
				passwd: password
			}
		}).then(function (result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	function signOut() {
		// TODO: need an actual implementation of this
		var deferred = $q.defer();
		deferred.resolve('success');
		return deferred.promise;
	};
	
	function changePassword(oldPassword, newPassword) {
		var deferred = $q.defer();
		
		$http.get('/thrift.php', {
			params: {
				action: 'changePasswd',
				oldpasswd: oldPassword,
				newpasswd: newPassword,
				id: $model.getToken()
			}
		}).then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	function forgotPassword() {
		// TODO: need an actual implementation of this
		var deferred = $q.defer();
		deferred.resolve('success');
		return deferred.promise;
	};
	
	function getItems() {
		var deferred = $q.defer();
		
		$http.get('/thrift.php', {
			params: {
				action: 'list_items',
				id: model.getToken()
			}
		}).then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	function getItem(item) {
		var deferred = $q.defer();
		
		$http.get('/thrift.php', {
			params: {
				action: 'get',
				item: item,
				id: model.getToken()
			}
		}).then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	return {
		signIn: signIn,
		signOut: signOut,
		changePassword: changePassword,
		forgotPassword: forgotPassword,
		getItems: getItems,
		getItem: getItem
	};
});