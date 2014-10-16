app.factory('client', function($http, $q, model) {
	function signIn(email, password) {
		var deferred = $q.defer();
		
		$http.post('/api/auth', {
			email: email,
			password: password
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
		
		$http.put('/api/user', {
			oldPassword: oldPassword,
			newPassword: newPassword
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
		
		$http.get('/api/listings')
		.then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	function getItem(item) {
		var deferred = $q.defer();
		
		$http.get('/api/listing/' + item)
		.then(function(result) {
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