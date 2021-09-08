'use strict';

angular.module('logoutButton').component('logoutButton', {
    templateUrl: './components/logout-button/logout-button.template.html',

    controller: function LogoutFormController($http, $location) {
        var self = this;
    
        self.clickLogout = function () {

            $http.get('./php/logout.php')
                .then(function (response) {
                    console.log(response.data);
                    $location.path('/login');
                });
            
        }
    
    }
});
