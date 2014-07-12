app.controller('CategoriesController', ['$scope', 'Category', '$location', '$routeParams','$route', function($scope, $location, Category, $routeParams, $route) {
    Category.start($routeParams.id).then(function (datas) {
        console.log(datas);
    }, function (message) {
        console.log(message);
    });
}]);