'use strict';

angular.module('doctor').component('doctor', {
    templateUrl: './components/doctor/doctor.template.html',

    controller: function DoctorController($http) {

        
        var self = this;

        $http.get('./php/exams.php')
            .then(function (response) {

                console.log(response.data);

                self.exams = response.data;

                if (response.data['category'] === 'doctor') {
                    // Display doctor page
                } else {
                    // Inform user he's unauthorized
                }
            });
        
        self.schedule = function () {

            // Check if a radiology examination checkbox has been checked

            var empty = self.exams.every(function (exam) {
                return !document.getElementById(exam.name).checked;
            });

            if (empty) {
                document.getElementById('order-prompt').innerHTML
                    = 'you should check at least one examination checkbox';
                    
                document.getElementById('order-prompt').style = "color: crimson";
                
                Array.from(document.getElementsByClassName('input-checkbox-container'))
                    .forEach(function (examContainer) {
                        examContainer.style = 'border: solid crimson 1.25px';
                    });
                    
                return;
            }


            // Construct json request data representation object

            var selectedExams = self.exams.filter(function (exam) {
                return document.getElementById(exam.name).checked;
            });

            var json = JSON.stringify({
                
                'patient': {
                    'firstName': self.firstName,
                    'lastName': self.lastName,
                    'fatherName': self.fatherName,
                    'motherName': self.motherName,
                    'ssn': self.ssn,
                    'address': self.address
                },

                'order': {
                    'reason': self.reason,
                    'recommendedDate': self.recommendedDate,
                    'priority': self.priority,
                    'selectedExams': selectedExams
                }

            });

            // console.log(json);

            $http.post('./php/schedule.php', json)
                .then(function (response) {
                    console.log(response.data);
                    self.results = response.data;
                });

            
                // console.log((self.results));

                document.getElementById('doctor-form').style='display: none';

                Array.prototype.forEach.call(document.getElementsByClassName('result'), elem=>{
                    elem.style = 'display: visible';
                });

                document.getElementById('order-prompt').innerHTML = 'below you can see the list of your scheduled appointments';   
                console.log("CHAGNE DETECT");
        }
        
    }
});
