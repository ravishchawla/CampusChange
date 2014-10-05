var app = angular.module('ThriftShopApp', ['ui.router', 'ngMockE2E']);

app.controller('MainController', function ($scope) {
	
});

app.controller('ResultsController', function ($scope, $state, client) {
	var request = client.getItems();
	request.then(function (items) {
		$scope.items = items;
	}, function (error) {
		$state.go('main.error');
	});
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
	var email = null;
	var token = null;
	
	return {
		isAuthenticated: function () {
			return (token != null);
		},
		getToken: function() {
			return token;
		},
		getEmail: function() {
			return email;
		},
		signIn: function(email, token) {
			email = email;
			token = token;
		},
		signOut: function() {
			username = null;
			token = null;
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
		templateUrl: 'partials/main.html'
	})
	.state('error', {
		url: '/error',
		templateUrl: 'partials/main.error.html'
	})
	.state('main.account', {
		url: '/account',
		templateUrl: 'partials/main.account.html'
	})
	.state('main.results', {
		url: '/',
		templateUrl: 'partials/main.results.html',
		controller: 'ResultsController'
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
	$rootScope.$on('$stateChangeStart', function(event, toState, toStateParameters) {
		$rootScope.toState = toState;
		$rootScope.toStateParameters = toStateParameters;
		
		if (toState.name !== 'signin' && !model.isAuthenticated()) {
			$state.go('signin');
		}
	});
});