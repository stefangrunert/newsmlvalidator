/* global angular */
var newsmlvalidator = angular.module("nmlv", ['truncate', 'ui.ace']);
newsmlvalidator.controller("nmlvCtrl", function ($scope, $http) {
    $scope.validationActive = false;
    $scope.tabfoo = 'newsml';
    $scope.annotations = [];
    $scope.editor = null;
    $scope.markers = [];
    $scope.validations = [
        {
            name: 'NewsML',
            id: 'newsml',
            res: {}
        },
        {
            name: 'Embedded XHTML content',
            id: 'html5',
            res: {}
        },
        {
            name: 'Microdata',
            id: 'microdata',
            res: {}
        },
        {
            name: 'NITF',
            id: 'nitf',
            res: {}
        }
    ];

    $scope.setTabfoo = function(val) {
        $scope.tabfoo = val;
    }


    $scope.options = [
        {name: "--select example--", fileName: false},
        {name: "NewsML-G2 NewsMessage, containing HTML 5 polyglot, Microdata", fileName: 'NewsML-G2-valid01.xml'},
        {name: "NewsML-G2 NewsMessage + HTML 5 polyglot + a lot of rNews Microdata", fileName: 'NewsML-G2-rnews.xml'},
        {
            name: "Invalid NewsML-G2 NewsMessage, containing HTML 5 polyglot, Microdata",
            fileName: 'NewsML-G2-invalid01.xml'
        },
        {
            name: "NewsML-G2 NewsMessage, containing XHTML1.0 (DPA)",
            fileName: 'urn_newsml_dpa.com_20090101_130821-96-00675.xml'
        },
        {
            name: "NewsML-G2 NewsMessage, containing XHTML1.0 (Reuters)",
            fileName: '2013-10-21T192044Z_1670105467_L1N0IB1MQ_RTRMADT_0_BBO-SOX-CARDINALS-NEWS.XML'
        }

    ];
    $scope.selectedOption = $scope.options[0];

    $scope.expample = [
        {name: 'Invalid', value: 'NewsML-G2-invalid01.xml'},
        {name: 'Valid', value: 'NewsML-G2-valid01.xml'}
    ];

    $scope.validate = function () {
        if (!$scope.payload || $scope.payload == '') {
            return;
        }
        $scope.clearAnnotations()
        resetResults();
        var annotations = [];

        activateValidations(true, 'newsml');
        $scope.validationActive = true;
        var url = 'validator.php?appRequest';
        var req = {
            method: 'POST',
            url: url + '&standard=NewsML',
            headers: {
                'Content-Type': 'text/html'
            },
            data: $scope.payload
        };

        // validate NewsML
        $http(req).then(function (res) {
            $scope.validations[0].res = res.data;
            $scope.validations[0].loader = false;
            $scope.processAnnotations(res.data.validationResults);
            $scope.setAnnotations();

            // validate HTML
            req.url = url + '&standard=HTML';
            activateValidations(true, 'html5');
            $http(req).then(function (res) {
                $scope.validations[1].res = res.data;
                $scope.validations[1].loader = false;
                $scope.processAnnotations(res.data.validationResults);
                $scope.setAnnotations();
            });

            //validate Microdata
            req.url = url + '&standard=Microdata';
            activateValidations(true, 'microdata');
            $http(req).then(function (res) {
                $scope.validations[2].res = res.data;
                $scope.validations[2].loader = false;
                $scope.processAnnotations(res.data.validationResults);
                $scope.setAnnotations();
            });

            //validate NITF
            req.url = url + '&standard=NITF';
            activateValidations(true, 'nitf');
            $http(req).then(function (res) {
                $scope.validations[3].res = res.data;
                $scope.validations[3].loader = false;
                $scope.processAnnotations(res.data.validationResults);
                $scope.setAnnotations();
            });
        });
    };

    $scope.processAnnotations = function (validationResults) {
        if (!validationResults) {
            return;
        }
        validationResults.forEach(function (validationResult) {
            var offsetLine = validationResult.documentOffsetLine;
            if (validationResult && validationResult.errors) {
                validationResult.errors.forEach(function (error) {
                    $scope.annotations.push(
                        {
                            row: offsetLine * 1 + error.line * 1,
                            column: error.column,
                            text: error.message,
                            type: "error"
                        }
                    )
                });
            }
        });
    }

    $scope.setAnnotations = function () {
        window.setTimeout(function () {
            $scope.editor.getSession().setAnnotations($scope.annotations);
                var Range = ace.require("ace/range").Range;
                $scope.annotations.forEach(function (annotation) {
                    var r = new Range(annotation.row, 0, annotation.row, 200)
                    $scope.markers.push($scope.editor.session.addMarker(r, "ace_selected-word", "fullLine"));
            })
        }, 500);
    }

    $scope.clearAnnotations = function () {
        $scope.editor.getSession().clearAnnotations();
        $scope.annotations = [];
        while($scope.markers.length > 0) {
            $scope.editor.session.removeMarker($scope.markers.pop());
        }
    }

    $scope.clear = function () {
        $scope.payload = '';
        $scope.validationActive = false;
        $scope.clearAnnotations();
        resetResults();
    };

    $scope.loadExample = function (callback) {
        if (!$scope.selectedOption.fileName) {
            return;
        }
        $scope.clear();
        $http.get('examples/' + $scope.selectedOption.fileName + '?' + Date.now()).success(function (data) {
            $scope.payload = data;
            $scope.validationActive = false;
            if (typeof callback == 'function') {
                callback();
            }

        });
    };

    $scope.aceLoaded = function (_editor) {

        // Editor part
        $scope.editor = _editor;
        _editor.setFontSize(15);

    };


    $scope.aceChanged = function (e) {
    };

    function resetResults() {
        $scope.validations.forEach(function (v) {
            v.res = {};
        });
        activateValidations(false);
    }

    function activateValidations(mode, validationId) {
        $scope.validations.forEach(function (v) {
            if (!validationId || validationId == v.id) {
                v.active = mode;
                v.loader = mode;
            }
        })
    }

    // for debugging:
    //angular.element(document).ready(function () {
    //    $scope.loadExample($scope.validate);
    //
    //});
});

