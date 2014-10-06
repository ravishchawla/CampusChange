var app = angular.module('ThriftShopApp', ['ui.router', 'ngMockE2E']);

app.controller('MainController', function ($scope) {
	$scope.back = function () {
		history.go(-1);
	}
});

app.controller('ResultsController', function ($scope, $state, $stateParams, client) {
	$scope.query = $stateParams.query;
	$scope.category = $stateParams.category;

	var request = client.getItems();
	request.then(function (items) {
		$scope.items = items;
	}, function (error) {
		$state.go('main.error');
	});
});

app.controller('ListingController', function ($scope, $state, $stateParams) {
	
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
		
		alert('sign up: not implemented');
	};
	
	$scope.signIn = function() {
		if (!validateForm()) return;
		
		var auth = client.signIn($scope.email, $scope.password);
		auth.then(function (token) {
			model.signIn($scope.email, token);
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

app.factory('model', function() {
	var model = {
		email: null,
		token: null,
	}
		
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
			model.email = email;
			model.token = token;
		},
		signOut: function() {
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
		templateUrl: 'partials/main.account.html'
	})
	.state('main.listings', {
		url: '/listings?category',
		templateUrl: 'partials/main.listings.html',
		controller: 'ResultsController'
	})
	.state('main.listing', {
		url: '/listings/{id}',
		templateUrl: 'partials/main.listing.html',
		controller: 'MainController'
	})
	.state('main.categories', {
		url: "/categories",
		templateUrl: 'partials/main.categories.html',
	})
	.state('main.post', {
		url: "/post",
		templateUrl: 'partials/main.post.html',
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