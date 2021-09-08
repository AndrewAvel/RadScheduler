'use strict';

angular.module('orderExam').component('orderExam', {
    templateUrl: './components/order-exam/orderExam.template.html',
    controller: ['$http', '$routeParams',
        function OrderExamController($http, $routeParams) {
            var self = this;

            self.orderExamId = $routeParams.orderExamId;


            var json = JSON.stringify(
                {
                    'orderExamId' : self.orderExamId
                }
            );

            $http.post('./php/order_exams.php', json)
                .then(function (response) {
                    console.log(response.data);

                    self.exam = response.data.exam;
                    self.order = response.data.order;
                    self.patient = response.data.patient;
                    self.order_exam = response.data.order_exam;

                });
        }
    ]
});
