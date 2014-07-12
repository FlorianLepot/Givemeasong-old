app.controller('HomeController', ['$scope', '$location', '$routeParams','$route', function($scope, $location, $routeParams, $route) {

    $scope.goToHomepage = function() {
        $(location).attr('href', "#/");
    };

    $scope.goToCategory = function(id) {
        $(location).attr('href', "#/letsgo/" + id);
    };

}]);