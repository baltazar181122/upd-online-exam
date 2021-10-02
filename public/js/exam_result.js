$( document ).ready(function() {
	$.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
     });

     batcth_details(4)
});


$('#batch').change(function(){
    // batch_details($(this).val());
    // console.log($(this).val());
    batcth_details($(this).val(), )
});

function batcth_details(data){
  console.log(data);
  oTable = $('#tableBatchReportDetails').DataTable( {
    "pageLength": 10,
    "aProcessing": true,
    "aServerSide": true,
    "orderCellsTop": true,
    "bDeferRender": true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        exportOptions: {
          columns: [ 0, 1, 2, 3 ]
        },
        className: 'btn btn-success',
        text: 'Print Batch Masterlist Result' 
      }
    ],
    "bDestroy": true,
    "ajax": {
      "url": "/batch/report/details/"+data,
      "dataSrc": ""
    },
    "columns": [
      {   
        "data":"first_name",
          "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
          {
            $(nTd).css('text-align', 'left');
            $(nTd).css('width', '40%');
          },
          "mRender": function( data, type, full ,meta) {
              return '<td>'+full.first_name+' '+full.last_name+'</td>';
          }
      },
      {   
        "data":"email",
          "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
          {
            $(nTd).css('text-align', 'left');
            $(nTd).css('width', '20%');
          },
          "mRender": function( data, type, full ,meta) {
              return '<td>'+full.exam_submitted+'</td>';
          }
      },
      {   
        "data":"result",
          "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
          {
            $(nTd).css('text-align', 'left');
            $(nTd).css('width', '10%');
          },
          "mRender": function( data, type, full ,meta) {
              return '<td>'+full.result+'</td>';
          }
      },
      {   
        "data":"result",
        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
        {
          $(nTd).css('text-align', 'left');
          $(nTd).css('width', '10%');
        },
        "mRender": function( data, type, full ,meta) {    
          return '<td><span class="label '+full.status+' bg-green">'+full.status+'</span></td>';
        }
      },
      {   
        "data":"result",
          "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
          {
            $(nTd).css('text-align', 'center');
            $(nTd).css('width', '5%');
          },
          "mRender": function( data, type, full ,meta) {
              return '<td><button onclick="viewExam('+full.exam_id +','+ full.user_id +','+full.batch_id+')" class="btn btn-warning  btn-sm"><span class="fa fa-eye"></span></button></td>';
          }
      },
      {   
        "data":"result",
          "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
          {
            $(nTd).css('text-align', 'center');
            $(nTd).css('width', '10%');
          },
          "mRender": function( data, type, full ,meta) {
              return '<td>'+
                      '<a href="/exam/details/print_exam/'+full.user_id+'/'+full.batch_id+'/'+full.exam_id+'" target="_blank"  class="btn btn-default  btn-sm"><span class="fa fa-file-excel-o"></span></a>'+
                      '| <a href="#" data-email="'+full.email+'"  data-user_id="'+full.user_id+'" data-exam_id="'+full.exam_id+'" class="btn btn-default  btn-sm mailto"><span class="fa fa-envelope"></span></a>'+
                      '</td>';
          }
      }         
    ],"columnDefs": [
    ]
  });
}

// function print_result(exam, user, batch){
//   var formData = new FormData();
//     formData.append('exam', exam);
//     formData.append('user', user);
//     formData.append('batch', batch);
//     $.ajax({
//       url: "/exam/details/print_exam",
//       type: "post",
//       data: formData,
//       async: false,
//       cache: false,
//       contentType: false,
//       processData: false,
//       dataType: "json",
//       success: function (data) {
//         console.log(data);
// 		  },
// 	  });
// }

function viewExam(exam, user, batch){
    var formData = new FormData();
    formData.append('exam', exam);
    formData.append('user', user);
    formData.append('batch', batch);
    $.ajax({
      url: "/exam/details",
      type: "post",
      data: formData,
      async: false,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        $('.detail_exam').html('');
        $.each(data, function( index, value ) {
            var result = '';
            if (value.result == 1) {
                result = '<span class="fa fa-check text-success"></span>'
            }else{
                result = '<span class="fa fa-times text-danger"></span>'
            }
            $('.detail_exam').append(
            '<tr>'+
              '<td>'+
              (value.questions === 'null' || value.questions === null ? value.question_id : value.questions) +
              '</td>'+
              '<td>'+
                value.answer_submited+
              '</td>'+
              '<td>'+
                result+
              '</td>'+
            '</tr>'
            );
          });

        $('#viewExam').modal('show');
		  },
	  });
}

function batch_details(data){
    var formData = new FormData();
    formData.append('batch_id', data);
	$.ajax({
		url: "/batch/details",
		type: "post",
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (data) {
            $('#effectivity_date').html(' '+data.effectivity_date);
		},
	});
}


$(document).on("click",".mailto",function() {
  console.log($(this).data('batch'));
  var formData = new FormData();
  formData.append('email', $(this).data('email'));
  formData.append('user_id', $(this).data('user_id'));
  formData.append('exam_id', $(this).data('exam_id'));
  $.ajax({
    url: "/exam-result/mailto",
    type: "post",
    data: formData,
    async: false,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (data) {
            $('#effectivity_date').html(' '+data.effectivity_date);
    },
  });
})