'use strict';

angular.module('radschedulerApp').config([
    '$routeProvider',
    function ($routeProvider) {
        $routeProvider
            .when('/login', {
                template: '<login><login>'
            })
            .when('/doctor', {
                template: '<doctor><doctor>'
            })
            .when('/radiologist', {
                template: '<radiologist><radiologist>'
            })
            .when ('/orderExams/:orderExamId', {
                template: '<order-exam></order-exam>'
            })
            .otherwise('/login');
    }
]);