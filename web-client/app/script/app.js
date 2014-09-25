var ThriftShopApp = angular.module('ThriftShopApp', ['ui.router']);

ThriftShopApp.config(function($stateProvider, $urlRouterProvider) {
	$urlRouterProvider.otherwise("/signin");
	
	$stateProvider
	.state('signin', {
		url: '/signin',
		templateUrl: 'partials/signin.html'
	})
	.state('account', {
		url: '/account',
		templateUrl: 'partials/account.html'
	})
	.state('main', {
		url: '/main',
		templateUrl: 'partials/main.html'
	})
	.state('main.categories', {
		url: "/categories",
		templateUrl: 'partials/main.categories.html',
	});
});
