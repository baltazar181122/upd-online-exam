$( document ).ready(function() {
	$.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
     });
    $('#active_batch').DataTable({
        "order": [[ 3, "asc" ]]
    });

    $('#tbl_reviewer').DataTable({
        "order": false
    });

    $('#tbl_exam').DataTable({
        "order": false
    });

    $('#tbl_result').DataTable({
        "order": false
    });
    
}); 

