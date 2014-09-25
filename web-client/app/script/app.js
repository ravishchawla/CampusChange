var ThriftShopApp = angular.module('ThriftShopApp', ['ngRoute']);

ThriftShopApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/categories', {
        templateUrl: 'partials/categories.html'
      }).
      when('/signin', {
		  templateUrl: 'partials/signin.html'
      }).
      otherwise({
        redirectTo: '/signin'
      });
  }]);