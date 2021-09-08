'use strict';

angular.module('login').component('login', {
    templateUrl: './components/login/login.template.html',

    controller: function LoginController($http, $location) {
        var self = this;

        self.login = function () {
            var self = this;

            var json = JSON.stringify({
                'username': self.username,
                'password': self.password
            });

            $http.post('./php/login.php', json)
                .then(function (response) {

                    console.log(response.data);

                    if (response.data['category'] === 'doctor') {
                        /**
                         * redirect to doctor page ...
                         * window.location='#pagename';
                         */

                        // window.location = '#!doctor';
                        $location.path('/doctor');
                    } else if (response.data['category'] === 'radiologist') {
                        /**
                         * redirect to radiologist ...
                         * window.location='#pagename';
                         */
                         $location.path('/radiologist');
                    } else {
                        let errorMessage = document.getElementById('error-message');

                        errorMessage.innerHTML = 'wrong username or password.';
                        errorMessage.style = 'color: crimson';
                    }
                });
        }
    }

});
