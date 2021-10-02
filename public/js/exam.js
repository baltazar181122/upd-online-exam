$( document ).ready(function() {
	$.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#tableExam').DataTable();
    $('#tableQuestions').DataTable();
	
	$('#reservationtime').daterangepicker({
        timePicker         : true,
        timePickerIncrement: 30,
        format             : 'YYYY-MM-DD HH:mm:ss'
	});
	$('#reservationtime2').daterangepicker({
        timePicker         : true,
        timePickerIncrement: 30,
        format             : 'YYYY-MM-DD HH:mm:ss'
    });
    $("#viewexamtitle").hide();
});

$('.add_exam_main').click(function(){
	$('input[name=exam_title]').val('');
	$('#exammainform_modal').modal('show');
});

$('#exammainform').submit(function(e){
    $("#btn_save_exam").prop("disabled");
	e.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: "/exam/saveExamMain",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
            $("#btn_save_exam").prop("disabled",false);
            swal("Good job!", "Successfully saved new exam!", "success")
            .then((value) => {
                $('#exammainform_modal').modal('hide');
                location.reload();
            })
        },
	});
});

$('.add_exam').click(function(){
    $("#exam_id").val('');
	$('input[name=file]').val('');
    $('#tableQuestions').DataTable();
	$('#examform_modal').modal('show');
    viewQuestions(0);
});

$('#examform').submit(function(e){
    $("#btn_upload").prop("disabled",true);
    console.log($("#exam_id").val());
	e.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: "/exam/save",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
            $("#btn_upload").prop("disabled",false);
			if (data[0] === 'error') {
				swal("Try Again!", data[1], "error");
			}
			else {
				swal("Good job!", "New Set Exam of Exam has been successfully saved!", "success")
				.then((value) => {
				    viewQuestions(data[1]);
                    $('input[name=file]').val('');
                    location.reload();
				});
			}

        },
        error: function(data){
            swal("Something Went Wrong!",'Pls check Exam Questionaire before upload', "error");
        }
	});
});

function viewQuestions(data, view, title, type){
    if (view === 1) {
        $("#examform").hide();
        $("#viewexamtitle").show();
        $("#exam_title_view").val(title);
    }
    else {
        $("#examform").show();
        $("#viewexamtitle").hide();
    }
    $('#examform_modal').modal('show');
    oTable =   $('#tableQuestions').DataTable( {
        "pageLength": 10,
        "aProcessing": true,
        "aServerSide": true,
        "orderCellsTop": true,
        "bDeferRender": true,
        // dom: 'Bfrtip',
        "bDestroy": true,
        "ajax": {
            "url": "/exam/question/view/"+data,
            "dataSrc": ""
        },
        "columns": [
            {
                "data":"id", 
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                {
                    $(nTd).css('text-align', 'left');
                    $(nTd).css('width', '5%');
                },
                "mRender": function( data, type, full ,meta) {
                    return '<td>'+full.id+'</td>';
                }
            },
            {   
                "data":"questions",
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                {
                    $(nTd).css('text-align', 'left');
                    $(nTd).css('width', '20%');
                },
                "mRender": function( data, type, full ,meta) {
                    return '<td>'+full.questions+'</td>';
                }
            },
            {   
                "data":"a",
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                {
                    $(nTd).css('text-align', 'left');
                    $(nTd).css('width', '20%');
                },
                "mRender": function( data, type, full ,meta) {
                    var res = "";
                    res = '<td>'+
                        '<ul>'+ 
                        '<li>'+full.a+'</li>'+
                        '<li>'+full.b+'</li>';
                    if (full.c !== null) {
                        res += '<li>'+full.c+'</li>';
                    }
                    if (full.d !== null) {
                        res += '<li>'+full.d+'</li>';
                    }
                    if (full.e !== null) {
                        res += '<li>'+full.e+'</li>';
                    }
                    if (full.f !== null) {
                        res += '<li>'+full.f+'</li>';
                    }
                    res += '</ul></td>';

                    return res;
                }
            },
            {   
                "data":"answer",
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                {
                    $(nTd).css('text-align', 'left');
                    $(nTd).css('width', '10%');
                },
                "mRender": function( data, type, full ,meta) {
                    return '<td>'+full.answer+'</td>';
                }
            },
            {   
                "data":"points",
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                {
                    $(nTd).css('text-align', 'left');
                    $(nTd).css('width', '10%');
                },
                "mRender": function( data, type, full ,meta) {
                    return '<td>'+full.points+'</td>';
                }
            },
            // {   
            //     "data":"answer",
            //     "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
            //         $(nTd).css('text-align', 'center');
            //         $(nTd).css('width', '20%');
            //     },
            //     "mRender": function( data, type, full ,meta) {
            //         return '<td> <button class="btn btn-default"><span class="fa fa-eye"></span></button></td>';
            //     }
            // },
        ],"columnDefs": [
        ]
    });

            $.ajax({
                url: "/exam/question/view/image/"+data,
                type: "get",
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (res) {
                   $('.img-div').html(res);
                },
            });
}

function deleteExam(id){
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this Exam!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            var formData = new FormData();
            formData.append('exam_id', id);
            $.ajax({
                url: "/exam/delete",
                type: "post",
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    location.reload();
                },
            });
        }
      });
}

// $(document).on('click', '.sent', function(){
// 	$('#overlay_modal').modal('hide');
// 	viewMasterlist($('#batch_id').val());
// 	$('#masterlist_modal').modal('show');
// });


$('.btn_exam_image').click(function(){
   $('#imageModal').modal('show');
});

$("#uploadFile").change(function(){
    $('#image_preview').html("");
    var total_file=document.getElementById("uploadFile").files.length;
    for(var i=0;i<total_file;i++)
    {
     $('#image_preview').append(
        '<div class="col-md-4">'+
         "<img src='"+URL.createObjectURL(event.target.files[i])+"' style='height:250px; width:250px;border: solid 1px gray;'>"+
         '</div>'
        );
    }
    $('.label_div').attr('hidden', false);
 });

//  $('form').ajaxForm(function(){
//    alert("Uploaded SuccessFully");
// }); 



$('#imageForm').submit(function(e){
    e.preventDefault();
    $('#overlay_load').fadeIn();
    // $('#imageModal').modal('hide');
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: "/images-upload",
        type: "post",
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
            $('#overlay_load').delay(1000).fadeOut('fast', function(){
                swal("Good job!", "Exam successfully saved!", "success")
					.then((value) => {
                        location.reload();
					});
                console.log(data);
                // $('#addImageModal').modal('show');
            });
        },
    });
});


// function viewImage(id, title){
//    $('#overlay_load').fadeIn();
//    $('#addImageModalLabel').text(title);
//    var formData = new FormData();
//    formData.append('id', id);
//    $.ajax({
//        url: "/exam/images/view",
//        type: "post",
//        data: formData,
//        async: false,
//        cache: false,
//        contentType: false,
//        processData: false,
//        dataType: "json",
//        success: function (data) {
//            $('#overlay_load').delay(1000).fadeOut('fast', function(){
//         //     <div class="col-md-4">
//         //     <img src="https://i.stack.imgur.com/34AD2.jpg" alt="exam Image" style="height: 250px !important;">
//         //   </div>
//                $('#addImageModal').modal('show');
//            });
//        },
//    });
// }

$('#count_label').keyup(function(){
    $('.label_count').html('');
    var count = $(this).val();
    for (var i = 1; i <= count; i++) {
        console.log('asd');
        $('.label_count').append(
            '<div class="col-md-12">'+
                '<div class="form-group row">'+
                '<label for="labelName" class="col-sm-1 col-form-label">No.'+i+'</label>'+
                '<div class="col-sm-9">'+
                    '<input type="text" class="form-control" id="labelname'+i+'" name="labelname'+i+'" placeholder="label Name" required value="image '+i+'">'+
                '</div>'+
                '<div class="col-sm-2">'+
                    '<input type="text" class="form-control" id="labelpoints'+i+'" name="labelpoints'+i+'" placeholder="label Points" value="1" required>'+
                '</div>'+
                '</div>'+
            '</div>'
        )
    }
});


$(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox({
      alwaysShowClose: true,
      css:'z-index: 2000'
      
    });
  });