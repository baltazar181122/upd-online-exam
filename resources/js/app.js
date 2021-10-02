// /**
//  * First we will load all of this project's JavaScript dependencies which
//  * includes Vue and other libraries. It is a great starting point when
//  * building robust, powerful web applications using Vue and Laravel.
//  */

// require('./bootstrap');

// window.Vue = require('vue');

// /**
//  * The following block of code may be used to automatically register your
//  * Vue components. It will recursively scan this directory for the Vue
//  * components and automatically register them with their "basename".
//  *
//  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
//  */

// // const files = require.context('./', true, /\.vue$/i);
// // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

// /**
//  * Next, we will create a fresh Vue application instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
//  */

// const app = new Vue({
//     el: '#app',
// });



$( document ).ready(function() {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
     
 });


 function icheck_(){
  $('input[type="radio"]').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass   : 'iradio_flat-red'
  })
 }



 if(document.getElementById("time") !== null){
  // window.onbeforeunload = function() {
  //   return "Time will continue , are you sure?";
  // };
  icheck_();

  var hr = 0;
  var min = 0;
  var sec = $('.time_' ).data( "time" );
  
  var count = ((hr*60)*60)+(min*60)+sec;
  
  var counter = setInterval(timer, 1000); //1000 will  run it every 1 second
  function timer() {
      count = count - 1;
      if (count == -1) {
          clearInterval(counter);
          return;
      }
      var seconds = count % 60;

      var minutes = Math.floor(count / 60);
      var hours = Math.floor(minutes / 60);
      minutes %= 60;
      hours %= 60;
  
      $('#time').html(hours + ":" + minutes + ":" + seconds); // watch for spelling
      if (count == 0) {
        swal({
          title: "Times Up!", 
          text: "Your exam will be submit, due to exceed to your time limit!", 
          type:"warning",
          allowOutsideClick: false,
        })
        .then((value) => {
          $( "#submit_exam" ).submit();
        });
      }
      
  }
}


function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.img-photo').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(".image").change(function(){
    readURL(this);
});

$('.change_photo').on('click', function(e){
  $(".image").click();
});

$('.profile_form').submit(function(e){
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
      url: "/student/profile/profile",
      type: "post",
      data: formData,
      async: false,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        if (data == "success") {
            swal("Success!", "Your profile successfully Update!", "success")
            .then((value) => {
                location.reload();
              });
        }
      },
    });
});

// function myFunction() {
//   var x = 1000
//   setTimeout(function(x){
//      console.log(x = x-1);
//   }, 1000);
// }

$('.btn-exam').click(function(e){
  e.preventDefault();
    // alert('asd');
    swal({
      title: "Are you sure?",
      text: "Once exam has stared, it can not be interupted!, please make sure you have stable internet, Thank you!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((start) => {
      if (start) {
        window.location.href = $(this).attr('data-url');
      }
    });
});

$(document).on('click', '[data-toggle="lightbox"]', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox({
    alwaysShowClose: true
  });
});