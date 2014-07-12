app.factory('Category', ['$http', '$q', '$route', function($http, $q, $route) {
    var factory = {
        category: false,
        start : function(categoryId) {
            var deferred = $q.defer();

            $http.get(Routing.generate('categories_start'), { params: { 'categoryId': categoryId } })
                .success(function (data, status) {
                    factory.category = data;
                    deferred.resolve(factory.category);
                })
                .error(function(data, status) {
                    deferred.reject(data);
                });

            return deferred.promise;
        }
    };

    return factory;
}]);