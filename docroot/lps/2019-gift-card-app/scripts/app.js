'use strict';

/**
 * @ngdoc overview
 * @name blackHawkApp
 * @description
 * # blackHawkApp
 *
 * Main module of the application.
 */
 
 jQuery(window).on('orientationchange', function() {
  window.location.reload();
}); 
 var $window = $(window);

 var  globalVars = { }
 // BEGIN CONFIGURABLE PATHS
   /* PLEASE RUN gulp AFTER YOU CHANGE ANY OF THE BELOW PATHS */
 
 // HERE YOU CAN CONFIGURE IMAGE PATH
 globalVars.imgpath = '../app/images/';
 
 // CONFIGURE VIEWS PATH HERE
 globalVars.viewpath = 'views/';


var blackHawk = angular
  .module('blackHawkApp', [
    'ngAnimate',
    'ui.router',
    'ngTouch'
  ]).run(function ($rootScope, GetPaloAltoData) {
    
    $rootScope.projectVars = {};
    // image path - already configured, no eeed to touch
    $rootScope.imgpath = globalVars.imgpath;

    // this path is only used for assets preloading - lines 50-67 below. 
    //  to change  font paths in css go to /app/styles/main.css lines 1-51
    $rootScope.fontspath =  '/app/fonts/';
    // '/app/fonts/' ; 
  
    // tracking variables
    $rootScope.projectVars.dynamicAssetState = 'landing-page'
    $rootScope.dynamicAssetState = 'landing-page'; 
    $rootScope.assetName = 'Generational Gift Card Buyers Interactive';
    $rootScope.projectAbreviation = 'BHM-01';
  
    // set paths for the assets that are preloaded
    $rootScope.projectVars.manifest = [
      {src: $rootScope.fontspath+"AauxProBold.ttf", id: "font0"}
       
     ]
  
     // PLEASE DO NOT CHANGE 
    $rootScope.activescene = 'home';  
    $rootScope.nextscene = 'none';
    $rootScope.nextTab = '';
    $rootScope.progresspoint = 0;
    $rootScope.resetAnimPoint = 0;
    $rootScope.slides= [ 'home', 'overview', 'insights', 'aileen', 'joshua', 'linda',  'susan', 'sebastian', 'julian', 'chris', 'end' ]
     


    $rootScope.setWebResourceObject = function (viewStateName, assetName, assetTopic) {
      var webresourceDataArray = {};
      webresourceDataArray['dynamicResource'] = {};
      webresourceDataArray['dynamicResource']['language'] = 'en_us';
      webresourceDataArray['event'] = 'dra-view';
      webresourceDataArray['dynamicResource']['viewStateName'] = viewStateName;
      webresourceDataArray['category'] = {};
      webresourceDataArray['category']['assetType'] = "dynamic resource asset";
      webresourceDataArray['category']['assetName'] = assetName;
      webresourceDataArray['category']['assetTopic'] = assetTopic;
      var webresourceDataJSON = JSON.stringify(webresourceDataArray);
      window.webresourceData = webresourceDataArray;
      $rootScope.dynamicAssetState = $rootScope.paloaltodata[$rootScope.activescene]['dynamic-asset-name'];
      // console.log($rootScope.dynamicAssetState)
       
    }
    $rootScope.setWebResourceObject('NIMS:landing-page', $rootScope.assetName, 'landing-page ');
  })
 

  .service('GetPaloAltoData', function ($rootScope) {
    this.jsondata = {
      "home": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
        "header-left": '<div class="igw-header-left-text">MEET YOUR GIFT CARD<br/>SHOPPER PERSONAS<div class="igw-header-left-white">Insights to Extend Reach and Grow Gift Card Revenue</div></div>',
        "header-right": ' <span class="igw-bold">The Shopping Enthusiast, the Coupon Clipper, the Anxious<br/> Researcher—our shopping personas influence whether<br/>we shop in store or online,</span> meticulously research every purchase or<br/>go with our gut, buy gift cards for our family and friends or just for<br/>ourselves. Like generations, shopping personas characterize us,<br/>though they are much, much more particular and, thus, revealing.', 
        "corner-bubble-bg": '#A8B400', 
        "corner-bubble-text": '', 
        "animations":[]
      },

      "overview": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
        "header-left": '<div class="igw-header-left-text02">OVERVIEW</div>',
        "header-right": '<div class="igw-header-right-overview"><span class="igw-bold">Whether buying gift cards for giving to others or<br/>for their own use, there are different ways that<br/>people use gift cards</span>—and their personas can shed<br/>some light on the subject</div>', 
        "corner-bubble-bg": '#0073BC', 
        "corner-bubble-text": '', 
	      "animations":[]
      },
      "insights": {
    
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
        "header-left": '<div class="igw-header-left-text02">GENERATIONAL<br/>INSIGHTS</div>',
        "header-right": '', 
        "corner-bubble-bg": '#0073BC', 
        "corner-bubble-text": '', 
	      "animations":[]
      },

      "aileen": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
        "header-left": '<div class="igw-header-left-text01">Aileen <span class="igw-header-left-text01a">the Shopping Enthusiast</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#025b81', 
        "corner-bubble-text": '<div class="aileen" data-modal="aileen"><div class="igw-bubble-text01">Aileen is a<br/>Millennial.</div><div class="igw-bubble-text02">—the generation most reliant on shopping recommendations from friends and family.</div><div class="igw-bubble-text03">Find out more about their<br/>shopping habits here.</div></div>', 
	      "animations":[]
      },

      "joshua": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Joshua <span class="igw-header-left-text01a">the Digital Outsourcer</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#0773ba', 
        "corner-bubble-text": '<div class="joshua" data-modal="joshua"><div class="igw-bubble-text01">Joshua is from<br/>Gen Z.</div><div class="igw-bubble-text02">—the generation most likely to purchase a gift card one or more times each month.</div><div class="igw-bubble-text03">Click here to learn<br/>more about their<br/>shopping habits.</div></div>',  
	      "animations":[]
      }, 

      "linda": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Linda <span class="igw-header-left-text01a">the Online Bargain Hunter</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#6bc8cb', 
        "corner-bubble-text": '<div class="linda"  data-modal="linda"><div class="igw-bubble-text01">Linda is a part <br/>of Gen X.</div><div class="igw-bubble-text02">—the generation most likely to research their purchases beforehand.</div><div class="igw-bubble-text03">Click here to learn<br/>more about their<br/>shopping habits.</div></div>', 
	      "animations":[]
      }, 

      "susan": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Susan <span class="igw-header-left-text01a">the Coupon Clipper</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#BCBE00', 
		"corner-bubble-text": '<div class="susan"  data-modal="susan"><div class="igw-bubble-text01">Susan is<br/>a Boomer.</div><div class="igw-bubble-text02">—the generation to whom value is the most important.</div><div class="igw-bubble-text03">Click here to learn<br/>more about their <br/>shopping habits.</div></div>', 
	      "animations":[]
      }, 
      "sebastian": {
    
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Sebastian<span class="igw-header-left-text01a">the Informed Independent</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#ed741b', 
		"corner-bubble-text": '<div class="sebastian" data-modal="sebastian"><div class="igw-bubble-text01">Sebastian<br/>is a Boomer.</div><div class="igw-bubble-text02">—the generation most likely to purchase a gift card one or more times each month.</div><div class="igw-bubble-text03">Click here to learn<br/>more about their<br/>shopping habits.</div></div>', 
 
	      "animations":[]
      }, 

      "julian": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Julian<span class="igw-header-left-text01a">the Anxious Researcher</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#990B3A', 
		"corner-bubble-text": '<div class="julian" data-modal="julian"><div class="igw-bubble-text01">Julian is a Millennial.</div><div class="igw-bubble-text02">——the generation least likely to make shopping lists.</div><div class="igw-bubble-text03">Learn more about<br/>their shopping<br/>habits here.</div></div>',
 
	      "animations":[]
      },

      "chris": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
		"header-left": '<div class="igw-header-left-text01">Chris<span class="igw-header-left-text01a">the Thoughtful Traditionalist</span></div>',
        "header-right": '', 
        "corner-bubble-bg": '#612178', 
		"corner-bubble-text": '<div class="chris"  data-modal="chris"><div class="igw-bubble-text01">Chris belongs<br/>to Gen X.</div><div class="igw-bubble-text02">—the generation most likely to consider themselves “bargain hunters.”</div><div class="igw-bubble-text03">Click here to learn<br/>more about their <br/>shopping habits.</div></div>',
   
	      "animations":[]
      },  

      "end": {
        "dynamic-asset-name" :  "landing-page",
        "view-state-name" : "landing-page",
        "header-left": '',
        "header-right": '',
        "corner-bubble-bg": '#0073BC', 
        "corner-bubble-text": '',  
	      "animations":[]
      }, 

    }

   
    this.getData = function () {
      return this.jsondata;
    }
    $rootScope.paloaltodata = this.jsondata;

  })
  .config(function($stateProvider, $urlRouterProvider) {

    $stateProvider.state('home' , { url:"/home",
       params: {  next:' next' },
         views: { 
       'main-app-div': { 
         templateUrl:  globalVars.viewpath + 'content.html',
        controller: 'contentCtrl'
       }
    }
  }) 

   $urlRouterProvider.otherwise('/home');  



  }) //END CONFIG
