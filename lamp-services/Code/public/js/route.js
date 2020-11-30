app.config(function($stateProvider, $urlRouterProvider,$httpProvider) {
//$httpProvider.defaults.headers.post['Access-Control-Allow-Origin'] = '*';
//$httpProvider.defaults.headers.post['dataType'] = 'json';
//$httpProvider.defaults.withCredentials = true;

$urlRouterProvider.otherwise('/home');

$stateProvider
.state('vehicles', {
  url:'/vehicles',
  controller:'AccordionDemoCtrl',
  templateUrl: 'Views/orders.html',
  
});
/*.state('logout', {
  url:'/logout',
  controller:'authCtrl',
  
});*/ 
})
