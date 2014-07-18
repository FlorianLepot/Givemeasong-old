var app = angular.module('gmasApp', ['ngRoute', 'ngAnimate']);

app.config(function($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: Routing.generate('homepage_content'),
            controller: 'CategoriesController'
        })
        .when('/letsgo/:category', {
            templateUrl: function(params){ return Routing.generate('homepage_content') },
            controller: 'CategoriesController'
        })
        .when('/listen/:playlist/:song', {
            templateUrl: function(params){ return Routing.generate('listen_content', {'playlistId': params.playlist, 'songId': params.song}) },
            controller: ''
        })
        .otherwise({ redirectTo: '/' });
});

app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

app.filter('capitalize', function() {
    return function(input, scope) {
        if (input !== undefined && input !== null) {
            return input.substring(0,1).toUpperCase() + input.substring(1);
        }
    };
});