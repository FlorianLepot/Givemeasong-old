var gmasApp = angular.module('gmasApp', ['ngRoute']);

gmasApp.config(function($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: Routing.generate('gmas_music_homepage_content'),
            controller: ''
        })
        .when('/init', {
            templateUrl: Routing.generate('gmas_music_homepage_content'),
            controller: 'initController'
        })
        .when('/listen/:playlist/:id', {
            templateUrl: Routing.generate('gmas_music_homepage_content'),
            controller: ''
        })
        .otherwise({ redirectTo: '/' });
});

gmasApp.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});

gmasApp.filter('capitalize', function() {
    return function(input, scope) {
        if (input !== undefined && input !== null) {
            return input.substring(0,1).toUpperCase() + input.substring(1);
        }
    };
});