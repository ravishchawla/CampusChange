app.controller('MainController', function ($scope) {
	$scope.back = function () {
		history.go(-1);
	}
});

app.controller('ListingsController', function ($scope, $state, $stateParams, client) {
	$scope.query = $stateParams.query;
	$scope.category = $stateParams.category;

	var request = client.getListings();
	request.then(function (listings) {
		$scope.listings = listings;
	}, function (error) {
		$state.go('main.error');
	});
});

app.controller('ListingController', function ($scope, $state, $stateParams, client) {
	var request = client.getListing($stateParams.id);
	request.then(function (item) {
		$scope.item = item;
	}, function (error) {
		$state.go('main.error');
	});
    
	var replies = client.getReplies($stateParams.id);
	request.then(function (replies) {
		$scope.replies = replies;
	}, function (error) {
		$state.go('main.error');
	});
});

app.controller('AccountController', function ($scope, $state, model, client) {
	$scope.email = model.getEmail();
	
	$scope.changePassword = function() {
		var req = client.changePassword($scope.oldPassword, $scope.newPassword);
		req.then(function () {
			alert('success');
		}, function () {
			alert('failure');
		});
	};
	
	$scope.signOut = function () {
		model.signOut();
		$state.go('signin');
	};
})

app.controller('CreateListingController', function ($scope, $state, client) {
    $scope.submit = function() {
        var listing = {
            title: $scope.title,
            description: $scope.description
        };
        
        var request = client.createListing(listing);
        request.then(function() {
            alert('success');
            $state.go('main.listings');
        }, function() {
            $state.go('main.error');
        });
    };
});

app.controller('SignInController', function ($scope, $state, model, client) {	
	function validateForm() {
		if (!$scope.email || !$scope.password) {
			alert('Please enter your username and password.');
			return false;
		} else if (!/\S+@gatech.edu/.test($scope.email)) {
			alert('A Georgia Tech email is required');
			return false;
		}
		
		return true;
	};
	
	$scope.signUp = function () {
		if (!validateForm()) return;
		
        var name = $scope.name;
        if (!name) {
            alert("Name required");
            return;
        }
        var names = name.split(" ");
		var signup = client.signUp($scope.email, $scope.password, names[0], names[1]);
		signup.then(function () {
            // attempt to sign in
    		var auth = client.signIn($scope.email, $scope.password);
    		auth.then(function (token) {
    			model.signIn($scope.email, token.token);
    			$state.go('main.categories');
    		}, function (error) {
    			alert('Something has gone wrong. Please try again.');
    		});
		}, function (error) {
			alert('Something has gone wrong. Please try again.');
		});
	};
	
	$scope.signIn = function() {
		if (!validateForm()) return;
		
		var auth = client.signIn($scope.email, $scope.password);
		auth.then(function (token) {
			model.signIn($scope.email, token.token);
			$state.go('main.categories');
		}, function (error) {
			alert('Invalid Username/Password');
		});
	};
	
	$scope.forgotPassword = function () {
		if (!$scope.email) {
			alert('Please enter your email.');
			return false;
		} else if (!/\S+@gatech.edu/.test($scope.email)) {
			alert('A Georgia Tech email is required');
			return false;
		}
		
		alert('forgot password: not implemented');
	}
});

app.factory('model', function($cookieStore, $http) {
	// TODO: model should be moved into rootscope so bindings work.
	//
	
	var sessionHeader = 'X-SessionId';
	
	var model = {
		email: $cookieStore.get('email'),
		token: $cookieStore.get('token'),
	};
	
	$http.defaults.headers.common[sessionHeader] = model.token;

	return {
		isAuthenticated: function () {
			return (model.token != null);
		},
		getEmail: function() {
			return model.email;
		},
		getToken: function() {
			return model.token;
		},
		signIn: function(email, token) {
			$http.defaults.headers.common[sessionHeader] = token;
			$cookieStore.put('email', email);
			$cookieStore.put('token', token);
			
			model.email = email;
			model.token = token;
		},
		signOut: function() {
			delete $http.defaults.headers.common[sessionHeader];
			$cookieStore.remove('email');
			$cookieStore.remove('token');
			
			model.username = null;
			model.token = null;
		}
	};
});

app.config(function($stateProvider, $urlRouterProvider) {
	$urlRouterProvider.otherwise("/signin");
	
	$stateProvider
	.state('signin', {
		url: '/signin',
		templateUrl: 'partials/signin.html',
		controller: 'SignInController'
	})
	.state('main', {
		url: '/main',
		templateUrl: 'partials/main.html',
		controller: 'MainController'
	})
	.state('main.error', {
		url: '/error',
		templateUrl: 'partials/main.error.html'
	})
	.state('main.account', {
		url: '/account',
		templateUrl: 'partials/main.account.html',
		controller: 'AccountController'
	})
	.state('main.listings', {
		url: '/listings?category',
		templateUrl: 'partials/main.listings.html',
		controller: 'ListingsController'
	})
	.state('main.listing', {
		url: '/listings/{id}',
		templateUrl: 'partials/main.listing.html',
		controller: 'ListingController'
	})
	.state('main.categories', {
		url: "/categories",
		templateUrl: 'partials/main.categories.html',
	})
	.state('main.post', {
		url: "/post",
		templateUrl: 'partials/main.post.html',
        controller: 'CreateListingController'
	});
});

app.run(function($rootScope, $state, model) {
	$rootScope.$on('$stateChangeStart', function(event, toState, toStateParameters, fromState) {
		$rootScope.toState = toState;
		$rootScope.toStateParameters = toStateParameters;
		
		if (toState.name !== 'signin' && !model.isAuthenticated()) {
			event.preventDefault();
			$state.go('signin');
		} 
	});
});