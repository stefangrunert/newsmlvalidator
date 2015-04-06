/* global angular */
var newsmlvalidator = angular.module("nmlv", []);
newsmlvalidator.controller("nmlvCtrl", function ($scope, $http) {
    $scope.validationActive = false;
    $scope.validations = [
        {
            name: 'NewsML',
            id: 'newsml',
            res: {}
        },
        {
            name: 'Polyglot HTML5',
            id: 'html5',
            res: {}
        },
        {
            name: 'Microdata',
            id: 'microdata',
            res: {}
        }
    ];

    $scope.validate = function () {
        if (!$scope.payload || $scope.payload == '') {
            return;
        }
        resetResults();
        activateValidations(true);
        $scope.validationActive = true;
        var req = {
            method: 'POST',
            url: 'validator.php?type=NewsML',
            headers: {
                'Content-Type': 'text/html'
            },
            data: $scope.payload
        };

        // validate NewsML
        $http(req).then(function (res) {
            $scope.validations[0].res = res.data;
            $scope.validations[0].loader = false;

            // validate HTML
            req.url = 'validator.php?type=HTML';
            $http(req).then(function (res) {
                $scope.validations[1].res = res.data;
                $scope.validations[1].loader = false;
            });

            //validate Microdata
            req.url = 'validator.php?type=Microdata';
            $http(req).then(function (res) {
                $scope.validations[2].res = res.data;
                $scope.validations[2].loader = false;
            });
        });
    };

    $scope.clear = function () {
        $scope.payload = '';
        $scope.validationActive = false;
        resetResults();
    };

    $scope.loadExample = function (nr, isValid, callback) {
        var mode = isValid ? 'valid' : 'invalid';
        $http.get('examples/NewsML-G2-' + mode + nr + '.xml?' + Date.now()).success(function (data) {
            $scope.payload = data;
            $scope.validationActive = false;
            if (callback) {
                callback();
            }
        });
    };

    function resetResults() {
        $scope.validations.forEach(function (v) {
            v.res = {};
        });
        activateValidations(false);
    }

    function activateValidations(mode) {
        $scope.validations.forEach(function (v) {
            v.active = mode;
            v.loader = mode;
        })
    }
});
