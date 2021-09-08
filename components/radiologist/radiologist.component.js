'use strict';

angular.module('radiologist').component('radiologist', {
    templateUrl: './components/radiologist/radiologist.template.html',
    controller: function RadiologistController($http) {
        var self = this;

        $http.get('./php/radiologist.php')
            .then(function (response) {

                self.radiologist = response.data.radiologist;
                self.order_exams = response.data.order_exams;
                
                console.log(self.order_exams);
            
            });
    }
});
