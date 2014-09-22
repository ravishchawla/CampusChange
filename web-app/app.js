
//MVC components in angular:

//Model- The Model is data in scope properties; scopes are attached to the DOM.
//View- The template (HTML with data bindings) is rendered into the View.
//Controller - The ngController directive specifies a Controller class; the class has methods that typically express the business logic behind the application.


//compositional-no main method so there can be as many modules as you want
var ngThriftShop = angular.module("ngThriftShop", ['ngResource']);

//routing:: each route has a template that will be displayed when its called and a controller that will act on that template.
ngThriftShop.config(function ($routeProvider) {
    $routeProvider
.when("/categories", {
    templateUrl: "categories-template.html",
    controller: "CatCtrl"
})
.when("/favorites", {
    templateUrl: "favorites-template.html",
    controller: "FavCtrl"
})
.otherwise({
    template: "<h1>Welcome - Choose from the options given above</h1>"
})
;

});


//Controllers should contain only business logic
//A controller (constructor) function is responsible for setting model properties and functions/behavior on its associated $scope.
//Directives should encapsulate DOM manipulation
ngThriftShop.controller("CatCtrl", function ($scope, $http) {

});

ngThriftShop.controller("FavCtrl", function ($scope, $http) {


});
	
    

