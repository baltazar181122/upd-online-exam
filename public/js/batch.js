$( document ).ready(function() {
	$.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
     });
    $('#tableBatch').DataTable({
    	"order": [[ 2, "asc" ]]
    });
    // $('#tableReviewer').DataTable();
    
	$('#reservationtime').daterangepicker({
        timePicker         : true,
        timePickerIncrement: 30,
        format             : 'YYYY-MM-DD HH:mm:ss'
	})
	
	$("#batch_transfer_id").select2({
		dropdownParent: $("#transferbatch_modal")
	});
});



function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.readAsDataURL(input.files[0]);
  }
}

$("#filename").change(function () {
  	readURL(this);
});




$('#batchform').submit(function(e){
	$("#btn_new_batch_record").click(function(){
		$("#btn_new_batch_record").prop("disabled", true);
	})
	e.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: "/batch/save",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			if (data == 'success') {
				swal("Good job!", "Batch Fil has Save!", "success")
				.then((value) => {
				    location.reload();
				    $("#btn_new_batch_record").prop("disabled", false);
				 });;
			}
		},
	});
});

function editBatch(data){
	var formData = new FormData();
	formData.append('data', data);
    $.ajax({
		url: "/batch/view",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data);
			$('#batchform_modal').modal('show');
			$('input[name=batch_code]').val(data.batch_code);
			$('input[name=batch_name]').val(data.batch_name);
			var range_date = data.effectivity_date+' - '+data.expiration_date;
			$('input[name=range_date]').val(range_date);
			$('input[name=id]').val(data.id);
      	}
    });
};

function addExam(id) {
	$('input[name=exam_start_end]').val('');
	$('input[name=result_date]').val('');

	var formData = new FormData();
	formData.append('data', id);
    $.ajax({
		url: "/batch/exam/loadData",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data, id);
			/*inputs*/
			$("input[name=effectivity_date_batch_exam]").val(data[0].effectivity_date);
			$("input[name=expiration_date_batch_exam]").val(data[0].expiration_date);
			$("input[name=batch_id_exam]").val(id);

			if (data[1]) {
				$("input[name=id]").val(data[1].id);
				$("#exam_id").val(data[1].exam_id).attr('selected','selected');
				$("#image_exam_id").val(data[1].image_exam_id).attr('selected','selected');
				var range_date = data[1].exam_start+' - '+data[1].exam_end;
				$('input[name=exam_start_end]').val(range_date);
				$('input[name=result_date]').val(data[1].result_date);
				$('input[name=timer]').val(data[1].timer);

			}
      	}
    });

	/*plugins*/
	$("#exam_id").select2({
		dropdownParent: $("#addexamform_modal")
	});	
	$('#reservationtime2').daterangepicker({
        timePicker         : true,
        timePickerIncrement: 30,
        format             : 'YYYY-MM-DD HH:mm:ss'
	});
	$('#datemask').inputmask('yyyy-mm-dd', { 'placeholder': 'yyyy-mm-dd' });
	$('[data-mask]').inputmask();
	/*modal*/
	$('#addexamform_modal').modal('show');
}

function addReviewer(id) {
	var formData = new FormData();
	formData.append('data', id);
    $.ajax({
		url: "/batch/exam/loadData",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			/*inputs*/
			console.log(data[0].effectivity_date);
			$('#batchreviewerform_modal').on('shown.bs.modal', function () {
				$("input[name=effectivity_date_reviewer]").val(data[0].effectivity_date);
				$("input[name=expiration_date_reviewer]").val(data[0].expiration_date);
				$("input[name=batch_id_reviewer]").val(id);
			})
			
      	}
    });

	
	/*plugins*/
	$('#reservationtime3').daterangepicker({
        timePicker         : true,
        timePickerIncrement: 30,
        format             : 'YYYY-MM-DD HH:mm:ss'
	});
	
	viewReviewer(id);

	/*modal*/
	$('#batchreviewerform_modal').modal('show');
}

function deleteReviewer(id) {
	if (confirm("Are you sure you want to delete?")) {
	    $.ajax({
			url: "/batch/reviewer/destroy/"+id,
			type: "post",
			// data: formData,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (data) {
				// console.log(data);
				if (data[0] === 'success') {
					swal("Good job!", "Successfully Deleted a File!", "success")
					.then((value) => {
						viewReviewer(data[1]);
					});
				}
	      	}
	    });
	}
}

$('#addexamform').submit(function(e){
	$("#btn_add_exam_to_batch").click(function(){
		$("#btn_add_exam_to_batch").prop("disabled", true);
	})
	e.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: "/batch/exam/save",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data);
			if (data[0] === 'error') {
				swal("Try Again!", data[1], "error");
				$("#btn_add_exam_to_batch").prop("disabled", false);
			}
			else {
				swal("Good job!", "Successfully Saved Exam!", "success")
				.then((value) => {
				    location.reload();
				    $("#btn_add_exam_to_batch").prop("disabled", false);
				});;
			}
		},
	});
});


$('#addreviewerform').submit(function(e){
	e.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: "/batch/reviewer/save",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data);
			if (data[0] === 'error') {
				swal("Try Again!", data[1], "error");
			}
			else {
				swal("Good job!", "Successfully Saved Exam!", "success")
				.then((value) => {
				    viewReviewer(data[1]);
				    $('input[name=filename]').val('');
				    $('input[name=start_end_date_reviewer]').val('');
				});
			}
		},
	});
});


$('#masterlistform').submit(function(e){
	e.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
		url: "/batch/masterlist/save",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data);
			if (data[0] == 'success') {
				swal("Good job!", "You clicked the button!", "success")
				.then((value) => {
					$('input[name=student_name]').val('');
					$('input[name=student_email]').val('');
				    viewMasterlist($('#batch_id').val());
				 });;
			}else if(data[0] == 'exist'){
				  swal({
					title: "User Email Already Exist from "+data.batch_name,
					text: "Transfer the user on this Batch?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				  })
				  .then((willDelete) => {
					if (willDelete) {

						var formData = new FormData();
						formData.append('batch_masterlist_id', data.user_id);
						formData.append('batch_transfer_id', data.batch);
						formData.append('transfer_student_email', data.email);
						// formData.append('request_batch', data.request_batch);
						$.ajax({
						url: "/batch/masterlist/save/transfer",
							type: "post",
							data: formData,
							async: false,
							cache: false,
							contentType: false,
							processData: false,
							dataType: "json",
							success: function (response) {
								swal("User Transfer successfully", {
									icon: "success",
								});
								viewMasterlist($('#batch_id').val());
							}
						});
					} 
				  });
			}else {
				swal("Unable to Proceed!", data[1], "error");
			}
      	},
    });
})

$('#transferbatch_form').submit(function(e){
	e.preventDefault();
	var formData = new FormData($(this)[0]);
    $.ajax({
		url: "/batch/masterlist/transfer",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			console.log(data);
			if (data[0] == 'success') {
				swal("Good job!", "You clicked the button!", "success")
				.then((value) => {
				    viewMasterlist($('#batch_id').val());
				    $("#transferbatch_modal").modal('hide');
				 });;
			}
			else {
				swal("Unable to Proceed!", data[1], "error");
			}
      	},
    });
})

$('#form_upload_masterlist').submit(function(e){
	e.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
		url: "/batch/masterlist/import",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data[0]['exist'])
			if (data[0]['exist']) {
				// swal("Good job!", "You clicked the button!", "success")
				// .then((value) => {
					$('.exist').html(data[0]['exist']);
					// $('input[name=file]').val('');
				    viewMasterlist($('#batch_id').val());
				//  });;
			}
      	},
    });
})

function viewMasterlist(data, tog){
	$("input[name=student_name]").val('');
	$("input[name=student_email]").val('');
	$('#batch_id').val(data);
	$('#batch_id_upload').val(data);
    oTable =   $('#tableMasterlist').DataTable( {
        "pageLength": 10,
        "aProcessing": true,
        "aServerSide": true,
        "orderCellsTop": true,
        "bDeferRender": true,
        dom: 'Bfrtip',
        buttons: [
            {
				text: '<span class="fa fa-envelope-o"></span></span>'+' Send Batch Email Registration',
				className: 'btn btn-primary tblsendbatchemail',
                action: function ( e, dt, node, config ) {
					$('#overlay_load').fadeIn();
					setTimeout(function(){
					var formData = new FormData();
					formData.append('id', $('#batch_id').val());
					$.ajax({
						url: "/batch/send-batch/mail",
						type: "post",
						data: formData,
						async: false,
						cache: false,
						contentType: false,
						processData: false,
						dataType: "json",
						success: function (response) {
							$('#overlay_load').delay(500).fadeOut('fast', function(){
								swal("Email Sent!", "All Student Successfully Emailed!", "success")
									.then((value) => {
										viewMasterlist(response, 0)
									});
								// console.log(data);
								// $('#addImageModal').modal('show');
								// alert(data);
								
							});
							
						  }
					});
				}, 500);
                }
            }
        ],
        "bDestroy": true,
        "ajax": {
        "url": "/batch/masterlist/view/"+data,
        "dataSrc": ""
        },
        "columns": [
			{   
                "data":"student_name",
                 "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                  {
					$(nTd).css('text-align', 'left');
					$(nTd).css('width', '10%');
                  },
                  "mRender": function( data, type, full ,meta) {
                      return '<td>'+full.student_name+'</td>';
                  }
              },
              {   
                "data":"email",
                 "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                  {
						$(nTd).css('text-align', 'left');
                        $(nTd).css('width', '10%');
                  },
                  "mRender": function( data, type, full ,meta) {
                      return '<td>'+full.email+'</td>';
                  }
			  },
			  
              {   
               "data":"email",
                "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
                 {
                     $(nTd).css('text-align', 'center');
                       $(nTd).css('width', '20%');
                 },
                 "mRender": function( data, type, full ,meta) {
					if (full.status == 0) {
						x = '<button class="btn btn-warning btn-sm"  onclick="sendRegLink('+full.id+')" class="update action btn btn-default">Send Link</button></td>'
					}else{
						x = '<button class="btn btn-danger btn-sm"  onclick="sendRegLink('+full.id+')" class="update action btn btn-default">Resent Link</button></td>'
					}
					if (!tog) {
					   return '<td> <button  onclick="viewUser('+full.id+')" class="update action btn btn-default btn-sm" title="View/Update Student"><span class="fa fa-eye"></span></button> | '+
					   '<button onclick="deleteStudent('+full.id+')" class="btn btn-danger btn-sm" title="Delete Student"><span class="fa fa-trash"></span></button> | '+
					   '<button onclick="transferStudent('+full.id+')" class="btn btn-success btn-sm" title="Transfer Student">Transfer</button> '+
					   ' | '+ x
					   ;
					}
					else {
						return "-"
					}
                   }
             },
       ],"columnDefs": [
        ]
  });
  	$('.tblsendbatchemail').removeClass('dt-button');

  	if (tog == null && !tog) {
		$("#btn_save_student").prop("disabled", false);
		$("#btn_save_masterlist").prop("disabled", false);
		oTable.buttons().enable();
	}
	else {
		console.log("test");
		$("#btn_save_student").prop("disabled", true);
		$("#btn_save_masterlist").prop("disabled", true);
		oTable.buttons().disable();
	}

	$('#masterlist_modal').modal('show');
}

function transferStudent(id) {
	var formData = new FormData();
	formData.append('data', id);
    $.ajax({
		url: "/batch/masterlist/loadData",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			console.log(data);
			$("input[name=batch_masterlist_id]").val(data.id);
			$("input[name=transfer_student_email]").val(data.email);
      	}
    });


	$("#transferbatch_modal").modal("show");
}

function viewReviewer(data) {
    $('#batchreviewerform_modal').modal('show');
    oTable =   $('#tableReviewer').DataTable( {
        "pageLength": 10,
        "aProcessing": true,
        "aServerSide": true,
        "orderCellsTop": true,
        "bDeferRender": true,
        "bDestroy": true,
        "ajax": {
            "url": "/batch/reviewer/view/"+data,
            "dataSrc": ""
        },
        "columns": [
			{   
				"data":"file_name",
				"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
					$(nTd).css('text-align', 'left');
					$(nTd).css('width', '50%');
				},
				"mRender": function( data, type, full ,meta) {
					return '<td>'+full.reviewer_name+'</td>';
				}
			},
			{   
				"data":"expiration_date",
				"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
					$(nTd).css('text-align', 'left');
					$(nTd).css('width', '30%');
				},
				"mRender": function( data, type, full ,meta) {
					return '<td>'+full.expiration_date+'</td>';
				}
			},
			{   
				"data":"batch_id",
				"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
					$(nTd).css('text-align', 'center');
					$(nTd).css('width', '20%');
				},
				"mRender": function( data, type, full ,meta) {
					return '<td> '+
					'<button class="btn btn-danger btn-sm" title="Delete" onclick="deleteReviewer('+full.id+')"><span class="fa fa-trash"></span></button></td>'+
					' | '+
					'<a download href="/reviewer/'+full.file_name+'" class="btn btn-sm btn-default" title="Download"><span class=" fa  fa-cloud-download"></span></a></td>';
				}
			},
        ],"columnDefs": [
        ]
    });
}

$('.add_batch').click(function(){
 	$.ajax({
 		url: "/batch/batchCode",
 		type: "post",
 		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			console.log(data);
			$('input[name=batch_code]').val(data);
		},
		error: function(data){
			console.log(data);
		}
 	});	

	// $('input[name=batch_code]').val('');
	$('input[name=batch_name]').val('');
	$('input[name=range_date]').val('');
	$('#batchform_modal').modal('show');
	$("input[name=id]").val('');
});


function sendRegLink(data){
	$('.email_url').html(
		'<center>'+
		'<img src="/assets/images/loading.gif" alt="" width="250">'+
		'</center>'
		);

	$('#masterlist_modal').modal('hide');
	$("#overlay_modal").modal({"backdrop": "static"});
	$('#overlay_modal').modal('show');
	setTimeout(function() {
	var formData = new FormData();
	formData.append('id', data);
    $.ajax({
		url: "/batch/masterlist/sende-mail-link",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			console.log(data)
			if (data == "success") {
				$('.email_url').html(
				'<center>'+
				'<img src="/assets/images/sent.gif" alt="" width="250">'+
				'<p><button class="btn btn-success btn-lg sent">Ok</button></p>'+
				'</center>'
				);
				
			}
		},
		error: function(data){
			console.log(data);
		}  
	});
	}, 500);
}

function viewUser(id){
	var formData = new FormData();
	formData.append('data', id);
    $.ajax({
		url: "/batch/masterlist/loadData",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
			// console.log(data);
			$("input[name=masterlist_id]").val(data.id);
			$("input[name=student_name]").val(data.student_name);
			$("input[name=student_email]").val(data.email);
      	}
    });
}

function deleteStudent(id) {
	if (confirm("Are you sure you want to delete?")) {
	    $.ajax({
			url: "/batch/masterlist/destroy/"+id,
			type: "post",
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (data) {
				// console.log(data);
				if (data[0] === 'success') {
					swal("Good job!", "Successfully Deleted a File!", "success")
					.then((value) => {
						viewMasterlist(data[1]);
					});
				}
	      	}
	    });
	}
}

$(document).on('click', '.sent', function(){
	$('#overlay_modal').modal('hide');
	viewMasterlist($('#batch_id').val());
	$('#masterlist_modal').modal('show');
});

// $(".disable-submit").submit(function ()
// {
//    $(this).closest('form').find(':submit').prop("disabled",true);
// });

$('#status').change(function(){
	if ($(this).val() == 1) {
		$('.new_').html(
			'<div class="row">'+
				'<div class="col-md-5">'+
				'<div class="form-group">'+
					'<label for="student_name">Student Name:</label>'+
					'<input type="text" name="student_name" class="form-control" placeholder="Student Name" required>'+
				'</div>'+
				'</div>'+
				'<div class="col-md-5">'+
				'<div class="form-group">'+
					'<label for="student_name">Student Email:</label>'+
					'<input type="email" name="student_email" class="form-control" placeholder="Student Name" required>'+
				'</div>'+
				'</div>'+
				'<div class="col-md-2">'+
				'<div class="form-group">'+
					'<button style="margin-top: 30px;" class="btn btn-block btn-primary"> <span class="fa fa-save"></span> Save</button>'+
				'</div>'+
				'</div>'+
			'</div>'
			);
	}else{
		$('.new_').html('');
	}
});

