$( document ).ready(function() {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $("#userstbl").DataTable();
 });

 function viewUser(data){
    var formData = new FormData();
    formData.append('data', data);
    $.ajax({
      url: "/user/view",
      type: "post",
      data: formData,
      async: false,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        console.log(data.first_name);
        $('#userview_modal').modal('show');
        $('input[name=firstname]').val(data.first_name);
        $('input[name=lastname]').val(data.last_name);
        $('input[name=email]').val(data.email);
        $('input[name=batch]').val(data.batch_id);
        $('input[name=id]').val(data.id);

      },
    });
 }

 $('#userform').submit(function(e){
     e.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: "/user/save",
        type: "post",
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
            if (data == 'success') {
                swal("Good job!", "You clicked the button!", "success")
                .then((value) => {
                    location.reload();
                  });;
               
            }
        },
      });
 });