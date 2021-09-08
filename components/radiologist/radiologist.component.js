'use strict';

angular.module('radiologist').component('radiologist', {
    templateUrl: './components/radiologist/radiologist.template.html',
    controller: function RadiologistController($http) {
        var self = this;

        $http.get('./php/radiologist.php')
            .then(function (response) {

                self.radiologist = response.data.radiologist;
                self.order_exams = response.data.order_exams;

                if (self.order_exams === null) {
                    let message = document.getElementById('radiologist-message');
                    message.innerHTML = 'you have no scheduled appointments';
                }
                
                console.log(self.order_exams);
            
            });
    }
});
