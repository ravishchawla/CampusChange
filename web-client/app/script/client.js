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
	
	function signUp(email, password, first, last) {
		var deferred = $q.defer();
		
		$http.post('/api/user', {
			email: email,
			password: password,
            fname: first,
            lname: last
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
	
	function getListings() {
		var deferred = $q.defer();
		
		$http.get('/api/listings')
		.then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
	function getListing(item) {
		var deferred = $q.defer();
		
		$http.get('/api/listings/' + item)
		.then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
	};
	
    function createListing(item) {
        var deferred = $q.defer();
		
		$http.post('/api/listings', item)
		.then(function(result) {
			deferred.resolve(result.data);
		}, function(error) {
			deferred.reject(error);
		});
		
		return deferred.promise;
    };
    
	return {
		signIn: signIn,
        signUp: signUp,
		signOut: signOut,
		changePassword: changePassword,
		forgotPassword: forgotPassword,
		getListings: getListings,
		getListing: getListing,
        createListing: createListing,
	};
});