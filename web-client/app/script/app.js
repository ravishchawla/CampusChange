var ThriftShopApp = angular.module('ThriftShopApp', ['ui.router']);

ThriftShopApp.controller('ItemCtrl', function ($scope) {
  $scope.items = [
    {'name': 'Nexus S',
     'summary': 'Fast just got faster with Nexus S.'},
    {'name': 'Motorola XOOM™ with Wi-Fi',
     'summary': 'The Next, Next Generation tablet.'},
    {'name': 'MOTOROLA XOOM™',
     'summary': 'The Next, Next Generation tablet.'},
     {'name': 'Nexus S',
      'summary': 'Fast just got faster with Nexus S.'},
     {'name': 'Motorola XOOM™ with Wi-Fi',
      'summary': 'The Next, Next Generation tablet.'},
     {'name': 'MOTOROLA XOOM™',
      'summary': 'The Next, Next Generation tablet.'},
      {'name': 'Nexus S',
       'summary': 'Fast just got faster with Nexus S.'},
      {'name': 'Motorola XOOM™ with Wi-Fi',
       'summary': 'The Next, Next Generation tablet.'},
      {'name': 'MOTOROLA XOOM™',
       'summary': 'The Next, Next Generation tablet.'},
       {'name': 'Nexus S',
        'summary': 'Fast just got faster with Nexus S.'},
       {'name': 'Motorola XOOM™ with Wi-Fi',
        'summary': 'The Next, Next Generation tablet.'},
       {'name': 'MOTOROLA XOOM™',
        'summary': 'The Next, Next Generation tablet.'},
	    {'name': 'Nexus S',
	     'summary': 'Fast just got faster with Nexus S.'},
	    {'name': 'Motorola XOOM™ with Wi-Fi',
	     'summary': 'The Next, Next Generation tablet.'},
	    {'name': 'MOTOROLA XOOM™',
	     'summary': 'The Next, Next Generation tablet.'},
  ];
});

ThriftShopApp.config(function($stateProvider, $urlRouterProvider) {
	$urlRouterProvider.otherwise("/signin");
	
	$stateProvider
	.state('signin', {
		url: '/signin',
		templateUrl: 'partials/signin.html'
	})
	.state('main', {
		url: '/main',
		templateUrl: 'partials/main.html'
	})
	.state('main.account', {
		url: '/account',
		templateUrl: 'partials/main.account.html'
	})
	.state('main.results', {
		url: '/',
		templateUrl: 'partials/main.results.html',
		controller: 'ItemCtrl'
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
