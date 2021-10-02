<hr>
<label style="color:#d39e00"> Existing Email:</label>
<table  class="table table-bordered table-hover dataTable">
    <thead>
    <tr>
        <th  style="width: 50%; text-align: center">
        Email
        </th>
        <th   style="width: 50%; text-align: center">
        Batch
        </th>
        <th   style="text-align: center">
        Action
        </th>
    </tr>
    <tbody>
            @foreach($exist as $user => $val)
            <tr>
            <td>
                {{$val['email']}}
            </td>
            <td>
                {{$val['batch']}}
            </td>
            <td class="action_td-{{ $val['x'] }}">
                @if($val['current_batch_id'] == $val['batch_transfer_id'])
                    <span class="abel pull-right bg-green">Already in Batch</span>
                @else
                    <button data-x="{{ $val['x'] }}" data-batch_transfer_id="{{ $val['batch_transfer_id'] }}" data-batch_masterlist_id="{{ $val['batch_masterlist_id'] }}" data-email="{{ $val['email'] }}" class="btn btn-warning btn-sm btn-transfer">Transfer Anyway</button>
                @endif
            </td>
            </tr>
            @endforeach
    </tbody>
    </thead>
</table>

<script>
$( document ).ready(function() {
    $('.btn-transfer').click(function(){
        $('.action_td-'+$(this).data('x')).html('<center>'
                +'<img src="/assets/images/loading_large.gif" width="25px">'
                +'</center>');
        var x = $(this).data('x');

        var formData = new FormData();
        formData.append('batch_transfer_id', $(this).data('batch_transfer_id'));
        formData.append('transfer_student_email', $(this).data('email'));
        formData.append('batch_masterlist_id', $(this).data('batch_masterlist_id'));
        $.ajax({
            url: "/batch/masterlist/save/transfer",
            type: "post",
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (resspone) {
                if (resspone[0] == 'success') {
                    console.log(resspone[0]);
                    setTimeout(function(){
                            $('.action_td-'+x).html('<center><span class="fa fa-check" style="color:green"></span></center>');
                       
                    }, 1500);
				    viewMasterlist($('#batch_id').val());
                }
                else {
                    swal("Unable to Proceed!", resspone[1], "error");
                }
            },
        });
    });
});
</script>


