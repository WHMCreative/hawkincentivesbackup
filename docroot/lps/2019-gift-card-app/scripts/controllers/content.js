'use strict';

/*var winHeightVH = $('.igw_slide').innerHeight()
var winHeightPC = $('.main-app-div ').innerHeight()

var mainHeightV = ((winHeightPC*100)/winHeightVH + "%")*/
/**
 * @ngdoc function
 * @name networkMythbustersp.controller:contentCtrl
 * @description
 * # contentCtrl
 * Controller of the networkMythbustersApp
 */
blackHawk.controller('contentCtrl', function ($rootScope, $scope, GetPaloAltoData) {

  var startAnimation = new  TimelineMax()
  
  
  .pause()

  $scope.projectVars.animations['startAnimation'] = startAnimation;

  var entryAnimation = new  TimelineMax()
   .fromTo('.igw-skew-block', 0.3, {opacity:0}, {opacity:1},0) 
    .fromTo('.igw-logo-blackhawk-white', 0.3, {opacity:0,y:-50}, {opacity:1,y:0},0.2) 
     .fromTo('.igw-header-left-text-intro', 0.3, {opacity:0,x:-50}, {opacity:1,x:0},0.4)  
  
 // .fromTo('.igw-header', 0.5, {opacity:0,height:'0vh'}, {opacity:1,height:'60vh'},0)
/*.fromTo('.igw-header-left-text', 0.5, {x:-100,opacity:0}, {x:0,opacity:1},0.5)  
 .fromTo('.igw-header-left-text > span', 0.5, {x:-100,opacity:0}, {x:0,opacity:1},0.8)   
 .fromTo('.igw-header-left-white', 0.5, {x:-100,opacity:0}, {x:0,opacity:1},1)   
*/

     .fromTo('.igw-slide-intro', 0.3, {autoAlpha:1},{autoAlpha:0},2.2) 
	 
 .fromTo('.igw-header-right-bg', 0.5, {opacity:0}, {opacity:1},2.5)
 .fromTo('.igw-header', 0.5, {opacity:0,height:'60vh'}, {opacity:1,height:'23vh'},2.5)
 // .fromTo('.igw-header-left', 0.5, { fontSize:'11vh',lineHeight: '11vh',width:'130vh',marginRight:'20vh'}, {fontSize:'6vh',lineHeight: '6vh',width:'75vh',marginRight:'75vh'},2.5)

 .fromTo('.igw-slide-home', 0.5, {backgroundColor:'#0773ba'}, {backgroundColor:'#fff'},2.5)
 // .fromTo('.igw-header-left-white', 0.5, {color:'#fff' ,lineHeight:'5vh', fontSize:'5.45vh' }, {color:'#A8B400',lineHeight:'3.5vh', fontSize:'2.96vh' },2.5)

  .fromTo('.igw-left-lm', 0.5, {left:'-15vh'}, {left:'0vh'},2)
  .fromTo('.igw-left-lm ', 0.5, {width:'10vh'}, {width:'48vh'},4)  
  .fromTo('.igw-left-lm div', 0.5, {opacity:0}, {opacity:1},4.5) 

  .fromTo('.igw-right-lm', 0.5, {right:'-15vh'}, {right:'0vh'},2)
  .fromTo('.igw-right-lm ', 0.5, {width:'10vh'}, {width:'57vh'},4.2)  
  .fromTo('.igw-right-lm div', 0.5, {opacity:0}, {opacity:1},4.7) 
    
  .fromTo('.igw-header-right', 0.5, {opacity:0,x:100}, {opacity:1,x:0, onComplete: function() {

    $scope.projectVars.animations['menuPopOut'].play()  

  } },3)
   .fromTo('.igw-home-text01', 0.5, {y:100,opacity:0}, {y:0,opacity:1},3)
   .staggerFromTo('.igw-person', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,3.7)
   .staggerFromTo('.igw-person-title>div', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,3.8)
  .pause()

  $scope.projectVars.animations['entryAnimation'] = entryAnimation;
 
  var menuPopOut = new  TimelineMax()
  .fromTo('.igw-left-menu', 0.5, {opacity:0}, {opacity:1},0)
  .pause(0)
  $scope.projectVars.animations['menuPopOut'] = menuPopOut;
  $scope.projectVars.animations['menuPopOut'].reverse()
  
var menuAnimation = new  TimelineMax()
 .fromTo('.igw-left-menu', 0.5, {left:"-40vh"}, { left:"0vh"},0)
 .fromTo('.igw-left-menu-arrow',0.3, { opacity:1}, {opacity:0},0)
 .fromTo('.igw-left-menu-arrow',0.01, { display:'block'}, {display:'none'},0.3)
 .pause()
 $scope.projectVars.animations['menuAnimation'] = menuAnimation;
 $scope.projectVars.animations['menuAnimation'].reverse()
 $scope.commonFunctions.toggleMenu = function() {     
 $scope.projectVars.animations['menuAnimation'].reversed() ? $scope.projectVars.animations['menuAnimation'].play() :  $scope.projectVars.animations['menuAnimation'].reverse();
  }

  $scope.commonFunctions.changebubble = function(slide, currentslide) { 
      

	
	var slideData = GetPaloAltoData.getData()[slide];
    if ( currentslide=='overview' || currentslide=='home' || currentslide=='end' || currentslide=='insights'){
		angular.element('.igw-corner-bubble .igw-skew-bubble').css('background-color', slideData['corner-bubble-bg'])
				     angular.element('.igw-corner-bubble .modal-popup').html(slideData['corner-bubble-text'])
		}  
	
	
    var enterHeader  =   new TimelineMax()
    .to('.igw-corner-bubble', 0.5,  {top:'74vh', right:'-6vh' , onComplete: function() {

    //  angular.element('.igw-corner-bubble').css('background-color', slideData['corner-bubble-bg'])

	  
     },onCompleteParams:[slideData, slide, headerAnim] },0).pause() 


     var exitHeader  =   new TimelineMax()
     .to('.igw-corner-bubble', 0.5,  {top:'74vh', right:'-50vh' },0).pause() 


    if ( ['overview', 'home', 'end', 'insights'].includes(slide) &&   ['overview', 'home', 'end', 'insights'].includes(currentslide) ) {  
     console.log('if')
       var a = 1;
   
      }

       else if ( !['overview', 'home', 'end', 'insights'].includes(slide) &&   ['overview', 'home', 'end', 'insights'].includes(currentslide) ) {  
        console.log('1 else if >> ' + currentslide )
        enterHeader.play()
     
        }

        else if(!['overview', 'home', 'end', 'insights'].includes(slide)  ) 
        { 
          console.log('2 else if>>' + currentslide)
          var headerAnim = new TimelineMax()
          .fromTo('.igw-corner-bubble', 0.5,  {top:'74vh', right:'-6vh' },  { top:'74vh', right:'-50vh', onComplete: function() {
        
            angular.element('.igw-corner-bubble .modal-popup').html(slideData['corner-bubble-text'])
     angular.element('.igw-corner-bubble .igw-skew-bubble').css('background-color', slideData['corner-bubble-bg'])
         //   angular.element('.igw-corner-bubble').css('background-color', slideData['corner-bubble-bg'])
            
               headerAnim.reverse()
            
          }, onCompleteParams:[slideData, slide, headerAnim] }, 0).pause() 


          headerAnim.play() 
       }
       
       else {  
        console.log('else') 
        exitHeader.play() }
  }



  $scope.commonFunctions.changeslideAnim = function(slide) { 
  
 // var slideData = GetPaloAltoData.getData()[slide];
if (slide=='home') {
  var changeslideAnim = new TimelineMax()
  .set('.igw-header',{ backgroundColor:'#0773ba'},0.5)
.set('.igw-header-right-bg',{ opacity:1},0.5)
.to('.igw-header',0.3, {height:'23vh'},0.5)
			.to('.igw-logo-blackhawk',0.2, { autoAlpha:1},0)
			.to('.igw-end-logo-blackhawk-white',0.2, { autoAlpha:0},0)
   		.fromTo('.igw-home-container',0.3, { opacity:0}, {opacity:1},0)
     	.fromTo('.igw-left-lm', 0.3, {left:'-15vh'}, {left:'0vh'},0)
  		.fromTo('.igw-left-lm ', 0.3, {width:'10vh'}, {width:'48vh'},0.3)  
  		.fromTo('.igw-left-lm div', 0.3, {opacity:0}, {opacity:1},0.7) 

 		 .fromTo('.igw-right-lm', 0.3, {right:'-15vh'}, {right:'0vh'},0)
 		 .fromTo('.igw-right-lm ', 0.3, {width:'10vh'}, {width:'57vh'},0.3)  
  		.fromTo('.igw-right-lm div', 0.3, {opacity:0}, {opacity:1},0.7) 
   		.fromTo('.igw-home-text01', 0.5, {y:100,opacity:0}, {y:0,opacity:1},0)
   		.staggerFromTo('.igw-person', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,0.3)
		.staggerFromTo('.igw-person-title>div', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,3.8)
}else if (slide=='overview'){

	var changeslideAnim = new TimelineMax()
  .set('.igw-header',{ backgroundColor:'#0073BC'},0.5)
  .set('.igw-header-right-bg',{ opacity:1},0.5)
.to('.igw-logo-blackhawk',0.2, { autoAlpha:1},0)
.to('.igw-end-logo-blackhawk-white',0.2, { autoAlpha:0},0)
   .to('.igw-header',0.3, {height:'17vh'},0.5)
	.fromTo('.igw-overview-container',0.3, { autoAlpha:0}, {autoAlpha:1},0)
	.fromTo('.igw-overview-headertext',0.3, { opacity:0}, {opacity:1},0.3)
	.fromTo('.igw-overview-vert',0.3, { opacity:0}, {opacity:1},0.7)	
	.staggerFromTo('.igw-overview-hor > div', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,0.5)
	
}else if (slide=='insights'){
	var changeslideAnim = new TimelineMax()
	 .set('.igw-header',{ backgroundColor:'#0073BC'},0.5)
     .set('.igw-header-right-bg',{ opacity:0},0.5)
	    .to('.igw-header',0.3, {height:'17vh'},0.5)
			.to('.igw-logo-blackhawk',0.2, { autoAlpha:1},0)
			.to('.igw-end-logo-blackhawk-white',0.2, { autoAlpha:0},0)
						
	.fromTo('.igw-insights-container',0.3, { autoAlpha:0}, {autoAlpha:1},0)
	.fromTo('.igw-insights-headertext',0.3, { opacity:0}, {opacity:1},0.3)
	.fromTo('.igw-insights-container01 .igw-insights-vert,.igw-insights-container01 .igw-insights-hor',0.3, { opacity:0}, {opacity:1},0.5)	
	.staggerFromTo('.igw-insights-container01 .igw-insights-graph > div', 0.5, {height: '0%'}, {height: '100%'},0.05,0.8)	
			   		
}else if (slide=='end'){
	var changeslideAnim = new TimelineMax()
	.set('.igw-header-right-bg	',{ opacity:0},0.5)
	.to('.igw-logo-blackhawk',0.2, { autoAlpha:0},0)
	   	.fromTo('.igw-end-container',0.3, { opacity:0}, {opacity:1},0)
		.fromTo('.igw-end-logo-blackhawk-white',0.3, { autoAlpha:0}, {autoAlpha:1},0.5)
		
	   .staggerFromTo('.igw-end-container > .center-position', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,0.3)
}else{
  var changeslideAnim = new TimelineMax()
.set('.igw-header',{ backgroundColor:'transparent'},0.5)
.set('.igw-header-right-bg	',{ opacity:0},0.5)
	.to('.igw-logo-blackhawk',0.2, { autoAlpha:0},0)
	.fromTo('.igw-slide-' + slide + ' .igw-slide-header',0.3, {height:'0vh'}, {height:'17vh'},0.5)
	
	.fromTo('.igw-' + slide +'-container',0.3, { opacity:0}, {opacity:1},0)
  .fromTo('.igw-' + slide +'-container',0.3, { opacity:0}, {opacity:1},0)
   .fromTo('.igw-slide-' + slide + ' .igw-middle-box',0.5, { opacity:0}, {opacity:1},0.3)  
   //.fromTo('.igw-slide-' + slide + ' .igw-box-01',0.5, { opacity:0}, {opacity:1},0.5)
   .fromTo('.igw-slide-' + slide + ' .igw-text-01',0.3, { opacity:0,x:50}, {opacity:1,x:0},0.7)     
   .fromTo('.igw-slide-' + slide + ' .igw-box-02',0.3, { opacity:0}, {opacity:1},0.9)   
   .staggerFromTo('.igw-slide-' + slide + ' .igw-box-01 > div',0.5, { opacity:0}, {opacity:1},0.1,1)
   .staggerFromTo('.igw-slide-' + slide + ' .igw-box-02 > div',0.5, { opacity:0}, {opacity:1},0.1,1)
   .staggerFromTo('.igw-slide-' + slide + ' .igw-opener-slide',0.5, { opacity:0}, {opacity:1},0.1,1.5)	
	
	}
  }


  $scope.commonFunctions.changeheader = function(slide) { 
      
    var slideData = GetPaloAltoData.getData()[slide];

    var headerAnim = new TimelineMax()
    
    .fromTo('.igw-header', 0.5, {top:'0%'}, {top:'-40%', onComplete:function() {
      angular.element('.igw-header-left').html(slideData['header-left'])
      angular.element('.igw-header-right').html(slideData['header-right'])
	  angular.element('.igw-header-left').css('border-right-color', slideData['corner-bubble-bg'])
	      if(slide =='home') { 
      angular.element('.igw-header-left').css('margin-bottom', '0vh')	
		  }else{
		  angular.element('.igw-header-left').css('margin-bottom', '5vh')		  
			  }
	    
    }, onCompleteParams:[slideData] }, 0)
    .to('.igw-header', 0.5, {top:'0%'}, 0.5 )
  }  

  var navAnim =  new TimelineMax()
  .fromTo('.igw-bottom-nav', 0.5, {bottm:'-40%'}, {bottom:'0%'})
  .pause()
  $scope.projectVars.animations['navAnim'] =  navAnim
  $scope.projectVars.animations['navAnim'].reverse()



//  ********************* POPUP  *********************//

$scope.commonFunctions.popup = function(popup) {

$scope.popupDialog = '.igw-dialog.' + popup 
$scope.popupDialogPerson = '.igw-person.igw-home-' + popup 
console.log($scope.popupDialog + ' + ' + $scope.popupDialogPerson)
angular.element('.igw-dialog,.igw-person').css('z-index', 0) 
angular.element($scope.popupDialog).css('z-index', 1)
angular.element($scope.popupDialogPerson).css('z-index', 1)  // close slide popup 
//console.log("clientX: " + event.clientX + " - clientY: " + event.clientY)

  $scope.popupAnimation = new  TimelineMax()
 .to('.igw-dialog', 0.3, {autoAlpha:0},0)
 .fromTo('.igw-person-title', 0.3, {opacity:1}, {opacity:0.3},0)
 .fromTo('.igw-dialog.' + popup , 0.2, {autoAlpha:0}, {autoAlpha:1},0)
 .fromTo('.igw-dialog.' + popup , 0.2, {autoAlpha:0}, {autoAlpha:1},0)
 .fromTo('.igw-dialog.' + popup +' .igw-dot' , 0.3, {scale:0}, {scale:1},0)
 .to('.igw-dialog.' + popup +' .igw-dot' , 0.3, {scale:0.5},0.3) 
 .fromTo('.igw-dialog.' + popup +' .igw-dialog-block ' , 0.3, {autoAlpha:0}, {autoAlpha:1},0.3)
 //.to('.igw-dialog-', 0.3, {autoAlpha:0},0)

 .pause()
$scope.popupAnimation.play()
	

//var activeDialog = popup	
	
} // end popup

$scope.commonFunctions.closePopup = function(popup) {
	angular.element('.igw-dialog,.igw-person').css('z-index', 0) 
		angular.element('.igw-opener-slide').removeClass('active')
  $scope.closePopup = new  TimelineMax()
  .to('.igw-person-title', 0.3, {opacity:1},0)
 .to('.igw-dialog.' + popup , 0.3,{autoAlpha:0},0)
 .to('.igw-dialog.' + popup +' .igw-dot' , 0.3, {scale:0},0) 
 .to('.igw-dialog.' + popup +' .igw-dialog-block ' , 0.3, {autoAlpha:0},0.3)
 .pause()
$scope.closePopup.play()
} 

//  ********************* SLIDE POPUP  *********************//

$scope.commonFunctions.popupSlide = function(popup) {
	angular.element('.igw-opener-slide').removeClass('active')
	angular.element('.igw-opener-' + popup).addClass('active')
  $scope.slidePopupAnimation = new  TimelineMax()
 .to('.igw-slide-dialog', 0.3, {autoAlpha:0},0)
 .fromTo('.igw-slide-dialog.' + popup , 0.2, {autoAlpha:0}, {autoAlpha:1},0)
/* .fromTo('.igw-slide-dialog.' + popup +' .igw-dot' , 0.3, {scale:0}, {scale:1},0)
 .to('.igw-dialog.' + popup +' .igw-dot' , 0.3, {scale:0.5},0.3) 
 .fromTo('.igw-dialog.' + popup +' .igw-dialog-block ' , 0.3, {autoAlpha:0}, {autoAlpha:1},0.3)*/
 //.to('.igw-dialog-', 0.3, {autoAlpha:0},0)

 .pause()
$scope.slidePopupAnimation.play()
	

//var activeDialog = popup	
	
} // end popup

$scope.commonFunctions.popupPopupSlide = function(popup) {
  $scope.popupPopupSlide = new  TimelineMax()
 .to('.igw-slide-dialog.' + popup , 0.3,{autoAlpha:0},0)
 /*.to('.igw-slide-dialog.' + popup +' .igw-dot' , 0.3, {scale:0},0) 
 .to('.igw-slide-dialog.' + popup +' .igw-dialog-block ' , 0.3, {autoAlpha:0},0.3)*/
 .pause()
$scope.popupPopupSlide.play()
} 



//  ********************* POPUP MODAL  *********************//

angular.element('.igw-corner-bubble i.up').on('click', function(){ 

angular.element('.igw-slide-dialog').css('visibility', 'hidden').css('opacity',0)  // close slide popup 
var modal = angular.element(this).parent().find( '.modal-popup > div' ).attr('data-modal')
console.log(modal)
   angular.element('.igw-corner-bubble .' + modal + ' .igw-bubble-text02').css('display', 'block')
   
 $scope.popupModalAnimation = new  TimelineMax()
  
 .to('.igw-modal', 0.3, {autoAlpha:0},0)
//.fromTo('.igw-modal.modal-' + modal + ' .igw-bubble-text02' , 0.2, {display:'none'}, {display:'block'},0)
 .fromTo('.igw-modal.modal-' + modal , 0.2, {autoAlpha:0}, {autoAlpha:1},0)
 .staggerFromTo('.igw-modal.modal-' + modal + ' .igw-modal-graph > div', 0.5, {width:'0%'}, {width:'100%'},0.05,0.5) 
  .to('.igw-corner-bubble .back' , 0.2, {display:'block' },0.2)
  .to('.igw-corner-bubble .up' , 0.2, {display:'none' },0) 
 
 .pause()
$scope.popupModalAnimation.play()
	
	
}) // end popup modal


// ************* CLOSE POPUP MODAL ****************//

$scope.commonFunctions.closePopupModal = function() {
	 angular.element('.igw-bubble-text02').css('display', 'none')
  $scope.closePopupModal = new  TimelineMax()
 .to('.igw-modal', 0.3,{autoAlpha:0},0)
   .to('.igw-corner-bubble .back' , 0.2, {display:'none' },0)
  .to('.igw-corner-bubble .up' , 0.2, {display:'block' },0.2) 
 .pause()
$scope.closePopupModal.play()
} 


//  ********************* OVERVIEW TOGGLE  *********************//

$scope.commonFunctions.toggleOverview = function(e) {
$scope.e = e

  $scope.toggleOverviewFirstAnim = new  TimelineMax()
	.fromTo('.igw-overview-navig01' , 0.3, {right:'0'}, {right:'-30vh'},0)
 	.fromTo('.igw-overview-navig02' , 0.3, {right:'-30vh'}, {right:'0'},0.3)
 	.fromTo('.igw-overview-container01' , 0.3, {autoAlpha:1}, {autoAlpha:0},0) 
 	.fromTo('.igw-overview-container02' , 0.3, {autoAlpha:0}, {autoAlpha:1},0.3)  
  	
	//.fromTo('.igw-overview-hor > div > span,.igw-overview-container02 .igw-overview-hor .igw-overview-graph' , 0.3, {opacity:1}, {opacity:0.4},3)   
	.staggerFromTo('.igw-overview-container02 .igw-overview-exmp', 0.5, {autoAlpha:0,x:50}, {autoAlpha:1,x:0},0.05,0.5) 
	.staggerFromTo('.igw-overview-container02 .igw-overview-graph  > div', 0.5, {height:'0%'}, {height:'100%'},0.05,1) 	



 .pause()

  $scope.toggleOverviewSecondAnim = new  TimelineMax()
 .fromTo('.igw-overview-navig02' , 0.3, {right:'0'}, {right:'-30vh'},0)
 .fromTo('.igw-overview-navig01' , 0.3, {right:'-30vh'}, {right:'0'},0.3)
 .fromTo('.igw-overview-container02' , 0.3, {autoAlpha:1}, {autoAlpha:0},0) 
 .fromTo('.igw-overview-container01' , 0.3, {autoAlpha:0}, {autoAlpha:1},0.3)  

	.fromTo('.igw-overview-container01 .igw-overview-vert',0.3, { opacity:0}, {opacity:1},0.6)	
	.staggerFromTo('.igw-overview-container01 .igw-overview-hor > div', 0.5, {autoAlpha:0,y:50}, {autoAlpha:1,y:0},0.05,0.9) 
 
 .pause()

 
if (e=='first') {$scope.toggleOverviewFirstAnim.play()}else{$scope.toggleOverviewSecondAnim.play()}
	

//var activeDialog = popup	
	
} // end popup

//  ********************* INSIGHTS TOGGLE  *********************//
$scope.commonFunctions.toggleInsights = function(e) {
$scope.e = e

  $scope.toggleInsightsFirstAnim = new  TimelineMax()
 .fromTo('.igw-insights-container01 ' , 0.5, {autoAlpha:0}, {autoAlpha:1},0) 
	.fromTo('.igw-insights-container01 .igw-insights-vert,.igw-insights-container01 .igw-insights-hor',0.3, { opacity:0}, {opacity:1},0.3)	
	.staggerFromTo('.igw-insights-container01 .igw-insights-graph > div', 0.5, {height: '0%'}, {height: '100%'},0.05,0.5)	
 .pause()



  $scope.toggleInsightsSecondAnim = new  TimelineMax()
 .fromTo('.igw-insights-container02 ' , 0.5, {autoAlpha:0}, {autoAlpha:1},0)  
	.fromTo('.igw-insights-container02 .igw-insights-vert02,.igw-insights-container02 .igw-insights-hor02',0.3, { opacity:0}, {opacity:1},0.3)	
	.staggerFromTo('.igw-insights-container02 .igw-insights-graph02 > div', 0.5, {height: '0%'}, {height: '100%'},0.05,0.5)	
 .pause()

  $scope.toggleInsightsThirdAnim = new  TimelineMax()
 .fromTo('.igw-insights-container03 ' , 0.5, {autoAlpha:0}, {autoAlpha:1},0)  
 .fromTo('.igw-insights-container03 .igw-insights-vert03,.igw-insights-container03 .igw-insights-hor03',0.3, { opacity:0}, {opacity:1},0.3)	
 .staggerFromTo('.igw-insights-container03 .igw-insights-graph03 > div', 0.5, {height: '0%'}, {height: '100%'},0.05,0.5)	
 .pause()
 
   $scope.toggleInsightsFourthAnim = new  TimelineMax()
  .fromTo('.igw-insights-container04 ' , 0.5, {autoAlpha:0}, {autoAlpha:1},0) 
  	.fromTo('.igw-insights-container04 .igw-insights-vert04,.igw-insights-container04 .igw-insights-hor04',0.3, { opacity:0}, {opacity:1},0.3)	
	.staggerFromTo('.igw-insights-container04 .igw-insights-graph04 > div', 0.5, {width: '0%'}, {width: '100%'},0.05,0.5)	
 .pause() 
 
if (e=='first') {
			$scope.toggleInsightsSecondAnim.reverse();
			$scope.toggleInsightsThirdAnim.reverse();
			$scope.toggleInsightsFourthAnim.reverse();
			angular.element('.igw-insights-navig01').removeClass('active')
			angular.element('.igw-insights-navig01.first').addClass('active')
			setTimeout(function(){$scope.toggleInsightsFirstAnim.reversed(true) ? $scope.toggleInsightsFirstAnim.play() : $scope.toggleInsightsFirstAnim.reverse();}, 500);
	}
else if (e=='second'){
			$scope.toggleInsightsFirstAnim.reverse();
			$scope.toggleInsightsThirdAnim.reverse();
			$scope.toggleInsightsFourthAnim.reverse();
			angular.element('.igw-insights-navig01').removeClass('active')
			angular.element('.igw-insights-navig01.second').addClass('active')
			setTimeout(function(){$scope.toggleInsightsSecondAnim.reversed(true) ? $scope.toggleInsightsSecondAnim.play() : $scope.toggleInsightsSecondAnim.reverse();}, 500);
}
else if (e=='third'){
			$scope.toggleInsightsFirstAnim.reverse();
			$scope.toggleInsightsSecondAnim.reverse();
			$scope.toggleInsightsFourthAnim.reverse();
			angular.element('.igw-insights-navig01').removeClass('active')
			angular.element('.igw-insights-navig01.third').addClass('active')
			setTimeout(function(){$scope.toggleInsightsThirdAnim.reversed(true) ? $scope.toggleInsightsThirdAnim.play() : $scope.toggleInsightsThirdAnim.reverse();}, 500);
	}
else {
			$scope.toggleInsightsFirstAnim.reverse();
			$scope.toggleInsightsSecondAnim.reverse();
			$scope.toggleInsightsThirdAnim.reverse();
			angular.element('.igw-insights-navig01').removeClass('active')
			angular.element('.igw-insights-navig01.fourth').addClass('active')
			setTimeout(function(){$scope.toggleInsightsFourthAnim.reversed(true) ? $scope.toggleInsightsFourthAnim.play() : $scope.toggleInsightsFourthAnim.reverse();}, 500);
	}
}






$scope.commonFunctions.changeslide = function(slide){     

console.log(slide + ' - ' +  $scope.e  )
	  angular.element('.igw-corner-bubble .back').css('display', 'none') 
	  angular.element('.igw-corner-bubble .up').css('display', 'block') 



  	$scope.commonFunctions.closePopup(slide) // close home popup

if (slide =='overview' &&  $scope.e =="first" ) {}
else if (slide =='insights' &&  $scope.e !="first" ) {console.log($scope.e)}
else{$scope.closeAllSlidePopup()} // close slide popup

	
	
     if(slide =='home' &&  $rootScope.activescene =="home" ) { 
      $scope.projectVars.animations['entryAnimation'].play()

    } else if ($rootScope.activescene !='home'){
	  //$scope.projectVars.animations['menuAnimation'].reversed() ? $scope.projectVars.animations['menuAnimation'].play() :  $scope.projectVars.animations['menuAnimation'].reverse(); 
	  

		}
   $scope.projectVars.animations['menuAnimation'].reverse(); 
    var slideData = GetPaloAltoData.getData()[slide];
	console.log(slide + ' - ' +$rootScope.activescene )
    if(slide == $rootScope.activescene) return;
    if(slide !='home') { 
 
      $scope.projectVars.animations['navAnim'].reversed() ?   $scope.projectVars.animations['navAnim'].play() :  $scope.projectVars.animations['navAnim'].play() 
	  angular.element('.igw-bottom-nav').css('display', 'block') 
    } else{
		angular.element('.igw-bottom-nav').css('display', 'none')  
		}
   
   
   if (slide == 'end') { 
  	 angular.element('.igw-next').css('display', 'none')
   }  
  
  else {   angular.element('.igw-next').css('display', 'block')  } 


     $rootScope.nextScene = slide; 
     $rootScope.activeSubScene = 0;
     $scope.previousScene =  $rootScope.activescene;
    var currentslide = angular.element('.igw-currentslide')
    var nextslide =  angular.element('.igw-slide-' + $rootScope.nextScene)
    nextslide.toggleClass('igw-nextslide', true)
    nextslide.toggleClass('igw-otherslide', false)


     var transitionAnim = new TimelineMax()
	 .to('.igw-' + slide +'-container',0,{opacity:0},0)
	 .to('.igw-slide-' + slide +' .igw-slide-header',0.3,{height:'0vh'},0.1)
	 .to('.igw-home-container',0.3,{opacity:0},0)	 
     .fromTo(currentslide, 0.5,{opacity:1}, { opacity:0, onComplete:function() {
      $scope.commonFunctions.changeheader(slide)
      $scope.commonFunctions.changeslideAnim(slide) // CHANGE SLIDE ANIMATION
      
      $scope.commonFunctions.changebubble(slide,$scope.previousScene)
       
      nextslide.toggleClass('igw-currentslide', true);
      nextslide.toggleClass('igw-nextslide', false);

      currentslide.toggleClass('igw-currentslide', false);
      currentslide.toggleClass('igw-otherslide', true);
      currentslide.css('opacity', 1);     
      
     }, onCompleteParams:[slide, $scope.previousScene] }, 0)


  
     var  assetTopic = slideData['dynamic-asset-name']
     var  viewstatte =  slideData['view-state-name']
    
     $rootScope.setWebResourceObject($rootScope.projectAbreviation+':' +viewstatte, $rootScope.assetName, assetTopic);
     $rootScope.activescene = slide;
    }


    $scope.commonFunctions.menuHover = function(inout) { 
     
     var img = 'menu-arrow.png';
     if( inout == 'in')  img = 'menu-arrow-hover.png';
     angular.element('.igw-left-menu-arrow img').attr('src', $rootScope.imgpath + '' + img)

    }


    $scope.commonFunctions.overviewHoverIn = function(element) { 
     var dataHoverOverview = angular.element(element).attr('data-slide')
      angular.element('.igw-overview-graph,.igw-overview-exmp').addClass('inactive')
     angular.element('.igw-overview-graph.gr-' + dataHoverOverview).addClass('active')
      angular.element('.igw-overview-exmp').addClass('inactive')
      angular.element('.igw-overview-exmp.' + dataHoverOverview).addClass('active')
    }
    $scope.commonFunctions.overviewHoverOut = function(element) { 
     var dataHoverOverviewOut = angular.element(element).attr('data-slide')
     angular.element('.igw-overview-graph.gr-' + dataHoverOverviewOut).removeClass('active')
      angular.element('.igw-overview-graph').removeClass('inactive')
     angular.element('.igw-overview-exmp').removeClass('active')	  
     angular.element('.igw-overview-exmp').removeClass('inactive')	
    }


    $scope.commonFunctions.homeClick = function() { 
	
      $scope.commonFunctions.changeslide('home');
     }
    
    $scope.commonFunctions.getNextSlide = function() { 
    
      var nextslideindex = $rootScope.slides.indexOf($rootScope.activescene)
      if (nextslideindex < $rootScope.slides.length) nextslideindex +=1
    
      var nextslide = $rootScope.slides[nextslideindex]
    
      return nextslide;
    
    }
    
    $scope.commonFunctions.getPreviousSlide = function() { 
      var nextslideindex = $rootScope.slides.indexOf($rootScope.activescene)
      if (nextslideindex > 0) nextslideindex -=1
    
      var nextslide = $rootScope.slides[nextslideindex]
     return nextslide;
    }
    
     
     $scope.commonFunctions.previousClick = function() { 
      var slideData = GetPaloAltoData.getData()[$rootScope.activescene];
      var nextslide = $scope.commonFunctions.getPreviousSlide();      
      $scope.commonFunctions.changeslide(nextslide)
      }
      
     $scope.commonFunctions.nextClick = function() { 
      var nextslide = $scope.commonFunctions.getNextSlide()
      $scope.commonFunctions.changeslide(nextslide)
      }

     $scope.closeAllSlidePopup = function() { 

		setTimeout(function(){
					angular.element('.igw-slide-dialog').css('visibility', 'hidden').css('opacity',0)  
					angular.element('.igw-modal').css('visibility', 'hidden').css('opacity',0)
					angular.element('.igw-overview-container > div,.igw-overview-navig').attr("style","");
					angular.element('.igw-insights-container > div,.igw-insights-navig').attr("style","");			
		}, 500);
			
      }



})


