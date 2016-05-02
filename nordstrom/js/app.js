var app = angular.module('nordapp', ['ui.jq', 'ui.bootstrap']).
  config(['$routeProvider', function($routeProvider) {
  $routeProvider.
      when('/', {templateUrl: 'template/main.html',   controller: MainCtrl}).
      when('/aboutyou', {templateUrl: 'template/aboutyou.html',   controller: AboutYouCtrl, resolve: AboutYouCtrl.resolve}).
      when('/skinconcern', {templateUrl: 'template/skinconcern.html',   controller: SkinConcernCtrl, resolve: SkinConcernCtrl.resolve}).
      when('/results', {templateUrl: 'template/results.html',   controller: ResultsCtrl, resolve: ResultsCtrl.resolve}).
      when('/', {templateUrl: 'template/main.html',   controller: MainCtrl}).
      otherwise({redirectTo: '/'});
}]);

app.value('uiJqConfig', {
    // The bxSlider namespace
    bxSlider: {
         // bxSlider options. This object will be used as the defaults
        minSlides: 4,
        maxSlides: 4,
        moveSlider: 2,
        slideWidth: 341,
        slideMargin: 12,
        infiniteLoop: false,
        hideControlOnEnd: true,
        pager: false
      },
    fancybox: {
      fitToView : true,
      width   : '95%',
      height    : '95%',
      autoSize  : false
    }
});

function AccordionCtrl($scope) {
  $scope.oneAtATime = true;

  $scope.groups = [
    {
      title: "Dynamic Group Header - 1",
      content: "Dynamic Group Body - 1"
    },
    {
      title: "Dynamic Group Header - 2",
      content: "Dynamic Group Body - 2"
    }
  ];

  $scope.items = ['Item 1', 'Item 2', 'Item 3'];

  $scope.addItem = function() {
    var newItemNo = $scope.items.length + 1;
    $scope.items.push('Item ' + newItemNo);
  };
}

var PopoverDemoCtrl = function ($scope) {
  $scope.dynamicPopover = "Hello, World!";
};

function MainCtrl($scope, $routeParams, $location, $http) {
	$scope.$emit('hideLoad');
    $scope.startEval = function() {
	  $scope.$emit('showLoad');
      $http({method: 'POST', url: '/kiosk/api/start'}).
      success(function(data, status, headers, config) {
        $location.path('/aboutyou');
      });
    }
}
function showLoadCtrl($scope, $timeout) {
	$scope.showLoad = true;
	$scope.showError = true;
	$scope.showTwo = true;
	$scope.$on('showLoad', function() {$scope.showLoad = false;});
	$scope.$on('hideLoad', function() {$scope.showLoad = true;});
	$scope.$on('showError', function() {	
	  $scope.showError = false; 
	  $timeout(function() { $scope.showError = true; }, 3000); 
	});
	$scope.$on('showTwo', function() {	
	  $scope.showTwo = false; 
	  $timeout(function() { $scope.showTwo = true; }, 3000); 
	});
	
}
function AboutYouCtrl($scope, $routeParams, $location, $http, aboutyou) {
    getCurrentIpadDate(); //Refresh Current Ipad Date
    $scope.questions = aboutyou;
	  $scope.$emit('hideLoad');
    $scope.user = {};
    $scope.answers = {};
    $scope.ids = {};
    $scope.showError = true;
    $scope.changeRadio = function(id) {
      $scope.answers[id] = true;
    }
    $scope.changeBox = function(id, ans) {
      if (isNaN($scope.answers[id]) ) {  $scope.answers[id] = 0 };
      if (typeof $scope.ids[ans] == "undefined") { $scope.ids[ans] = false; }
      
      if ($scope.ids[ans] != false) {
      $scope.answers[id] = $scope.answers[id] - 1;
      $scope.ids[ans] = false;
      } else {
      $scope.answers[id] = $scope.answers[id] + 1;
      $scope.ids[ans] = true;   
      }
    }
    $scope.showPop = false;
    $scope.toggle = function(event) {
      $scope.showPop = !$scope.showPop;
    }
	$scope.$on('$viewContentLoaded', function() {
});
    $scope.continueEval = function(user) {
    var count = 0;
    for (var k in $scope.user) {
    count++;
    }
    if (count < 3) {  $scope.showError = true; $scope.$emit('showError'); return false;  }
    count = 0;
    for (var k in $scope.answers) {
      
      if (count == 0 && $scope.answers[k] > 2) { $scope.$emit('showTwo'); return false; }
      if (k == false || $scope.answers[k] <= 0) {
       $scope.$emit('showError'); return false;
      }
      count++;
    }
    if (count < 4) { $scope.$emit('showError'); return false; }

     $scope.$emit('showLoad');
      $http({method: 'POST', url: '/kiosk/api/setData', data:user}).
      success(function(data, status, headers, config) {
        $location.path('/skinconcern');
      });
    }
}

// Wait for model to load before displaying content
AboutYouCtrl.resolve = {
  aboutyou : function($q, $http) {
        var deferred = $q.defer();
        $http({method: 'GET', url: '/kiosk/api/getAboutYouQuestions'})
        .success(function(data) {
                deferred.resolve(data)
            });
         return deferred.promise;
    }
    
}

function SkinConcernCtrl($scope, $routeParams, $location, $http, skinconcern) {
  $scope.questions = skinconcern;
  $scope.user = {};
  $scope.$emit('hideLoad');
  $scope.count = 0;
  $scope.answers = {};
    $scope.ids = {};
    $scope.changeBox = function(id, ans) {
      if (isNaN($scope.answers[id]) ) {  $scope.answers[id] = 0 };
      if (typeof $scope.ids[ans] == "undefined") { $scope.ids[ans] = false; }
      if ($scope.ids[ans] == true) {
        $scope.answers[id] = $scope.answers[id] - 1;
        $scope.ids[ans] = false;
      } else {
      $scope.answers[id] = $scope.answers[id] + 1;
      $scope.ids[ans] = true;   
      }
    }
    $scope.showPop = false;
    $scope.toggle = function(event) {
      $scope.showPop = !$scope.showPop;
    }
  for (var k in $scope.questions) {
    $scope.count++;
  }
  $scope.finishEval = function(user) {
  var count = 0;
  for (var k in $scope.answers) {
  if ($scope.answers[k] <= 0) {
        $scope.$emit('showError'); return false;
      }
      count++;
    }
  if (count < $scope.count) { $scope.$emit('showError'); return false; }
  $scope.$emit('showLoad');
      $http({method: 'POST', url: '/kiosk/api/finish', data:user}).
      success(function(data, status, headers, config) {
        $location.path('/results');
      });
    }
}
SkinConcernCtrl.resolve = {
  skinconcern : function($q, $http) {
        var deferred = $q.defer();
        $http({method: 'GET', url: '/kiosk/api/getSkinConcernQuestions'})
        .success(function(data) {
                deferred.resolve(data)  
            });
         return deferred.promise;
    }
    
}

function ResultsCtrl($scope, $routeParams, $location, $http, results) {
   $scope.results = results;
   $scope.prompt = false;
   $scope.success = true;
   $scope.printSuccess = true;
   $scope.printShow = false;
   $scope.$emit('hideLoad');

   $scope.print = function(id) {
   //console.log("print");
   $http({method: 'POST', url: '/kiosk/api/print'})
        .success();
   
   //document.location="kioskpro://kp_StarPrinter_printHtml&" + encodeURIComponent(id) + "?" + encodeURIComponent(1);
   kp_StarPrinter_printHtml(id, 1)
   
   if (!$scope.success) { $scope.success = true; }
   $scope.printSuccess = false;
   $scope.prompt = true;
   
   }

   $scope.checkEmail = function(email) {
   $scope.$emit('showLoad');
   console.log(email);
   $http({method: 'POST', url: '/kiosk/api/checkEmail', data:email}).
      success(function(data, status, headers, config) {
        
        if (data['status'] != 'sent') {
        $scope.email_error = true;
		
		    } else {
            $scope.email_ok = true;
               $scope.prompt = true;
               $scope.success = false;
		    }
		$scope.$emit('hideLoad');
        console.log(data);
      }).error(function(data, status, headers, config) {
        $scope.email_error = true;
		$scope.$emit('hideLoad');
        console.log(data);
      });
   }
   
}
ResultsCtrl.resolve = {
  results : function($q, $http) {
        var deferred = $q.defer();
        $http({method: 'GET', url: '/kiosk/api/getResults'})
        .success(function(data) {
                deferred.resolve(data)  
            });
         return deferred.promise;
    }
    
}


function CustomerInfo($scope, $routeParams, $http) {
  $scope.customer;
   $http({method: 'GET', url: '/kiosk/api/getCustomerInfo'})
        .success(function(data) {
                $scope.customer = data; 
            }).
  error(function(data, status, headers, config) {
  });
}


function QuickGuidesCtrl($scope, $routeParams, $http) {
  $scope.pdfs;
  $scope.showSubMenu = true;
  $scope.toggle = function() {
    $scope.showSubMenu = !$scope.showSubMenu;
    return false;
  }
    $http({method: 'GET', url: '/kiosk/api/getPdfs'})
        .success(function(data) {
                $scope.pdfs = data; 
            }).
  error(function(data, status, headers, config) {
       // alert(data);
	// alert(status);
  });
}

app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});
