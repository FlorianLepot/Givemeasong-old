app.controller('CategoriesController', ['$scope', '$routeParams', 'Category', '$route', '$location', function($scope, $routeParams, Category, $route, $location) {

    Category.getCategories().then(function (data) {
        $scope.categories = data.categories;
    })

    $scope.goToCategory = function (categoryId) {
        Category.start(categoryId).then(function (datas) {
            $location.path("listen/" + datas.playlist.token + "/" + datas.playlist.songs[0].id);
        }, function (message) {
            console.log(message);
        });
    }

    /*Category.start($routeParams.category).then(function (datas) {
        $location.path("listen/" + datas.playlist.token + "/" + datas.playlist.songs[0].id);
    }, function (message) {
        console.log(message);
    });*/
}]);