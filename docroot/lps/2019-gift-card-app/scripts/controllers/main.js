'use strict';

/**
 * @ngdoc function
 * @name networkMythbusters.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the networkMythbustersApp
 */
blackHawk.controller('MainCtrl', function ($rootScope, $scope, GetPaloAltoData) {

  $scope.commonFunctions = {}
  $scope.clickFunctions = {}
  $scope.projectVars = {}
  $scope.projectVars.animations = [];
  // this path is going to be used if we use templates
  $scope.projectVars.imgpath = "images/";

  var queue = new createjs.LoadQueue();
  queue.on("complete", handleComplete, this);
  queue.on("progress", handleProgress);
  queue.on("error", handleError);

  if (window.location.protocol == 'file:') {

    setTimeout(function () {
      handleComplete();
    }, 2000);
   
  } else { 
    /* alert('NOT FILE'); */
   
    queue.loadManifest($rootScope.projectVars.manifest, true);
  }

  function handleComplete() {
    var loaderTansition = new TimelineMax()
	/*	.fromTo('.loader-hidden', 1, { scale:0 	}, {  scale:1, transformOrigin: "50% 50%" }, 0)
    .fromTo('.loader', 1, { scale:1 	}, {  scale:0.2 }, 1)*/
    .fromTo('.igw-loader', 1, { opacity:1 }, {  opacity:0,   onComplete: function() {
    
      $scope.commonFunctions.changeslide('home');
    } },0) // izmeni vreme na 2
    .to('.igw-loader', 0.1, { display:'none' }, 0) // izmeni vreme na 3
    .pause(0)  
     loaderTansition.play();	 
  }

  function handleError(e) {
    console.log('preload error occured but never mind')
  }

  function handleProgress(event) {
    var pct = event.loaded * 100;
    pct = Math.round(pct);
    var  vw = pct / 4;
  }
  
  

  
});

  