<!DOCTYPE html>
<html lang="en">
<head>
  <title>To-do manager with laravel-9 and JQ</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <style>
        .slide-in {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1050; /* Bootstrap's default z-index for alerts */
        }
    </style>
</head>
<body>

<div class="container">          
  <table class="table table-hover">
    <thead>
      <tr>
        <th colspan="3"><h3>To Do List</h3></th>
        <th colspan="1">
          <input type="text" name="work" placeholder="Enter your work" id="work">
          <p class="text-danger" id="error"></p>
        </th>
      </tr>
      <tr>
        <th>Sn.</th>
        <th>Work</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="updated_Data">
      @php
        $count = 1;
      @endphp
      @if(count($work_data)>0)

          @foreach($work_data as $row)
            <tr>
              <td>{{ $count++; }}</td>
              <td>{{ $row['work_title'] }} </td>
              <td>
                  <input type="checkbox" id="mySwitch_{{ $row->id }}" onclick="actionUpdate({'action':'status', row:{{$row}} })" @checked($row->status) >
                  <label class="form-check-label" for="mySwitch_{{ $row->id }}" id="show_status_{{ $row->id }}">
                    @if($row->status=='1')
                      <span class="text-success">Completed</span>
                    @else
                      <span class="text-danger">Un-completed</span>
                    @endif
                  </label>
              </td>
              <td><input type="button" value="Delete" class="btn btn-danger" onclick="actionUpdate({'action':'delete', row:{{$row}} })"></td>
            </tr>



          @endforeach
      @else
          <tr>
              <td colspan="4" class="text-center">Data not found...!</td>
          </tr>
			@endif
    </tbody>
  </table>
</div>



<!-- <div class="container">
    <button id="showAlert" class="btn btn-primary">Show Alert</button>
</div> -->

<div id="alert" class="alert alert-success slide-in" role="alert">
    <span id="message"></span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

</body>
</html>


<script type="text/javascript">
      $(document).ready(function(){
          
          $('.close').on('click', function() {
              $('#alert').slideUp(); // Adjust duration as needed
          });
      });


  function actionUpdate(actionData){
    var formData = {
      row: actionData['row'],
      action: actionData['action'],
      _token: '{{ csrf_token() }}'
    };
    let eventRun = false;
    if(actionData['action']=="delete"){
      let confirmation = confirm("Are you sure you want to delete the work?");
      if (confirmation) {
        eventRun = true;
      }
    }else if(actionData['action']=="status"){
      if(actionData['row']['status']!='1'){
        $("#show_status_"+actionData['row']['id']).text("Completed");
        formData['status'] = '1';
      }else{
        $("#show_status_"+actionData['row']['id']).text("Un-completed");
        formData['status'] = '0';
      }
      eventRun = true;
    }

    if(eventRun == true){ // action for status update and delete
        $.ajax({
            url: "{{ route('todos.update') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
              if(response['status']==false){ // validation
                $("#error").text(response['validation']['work_title']);
              }else{
                $('#alert').slideDown(300);
                $("#message").text(response['message']);

                let html = '';
                $.each(response['data'], function(key, value){
                  html +='<tr>';
                    html +='<td>'+ parseInt(key+1) +'</td>';
                    html +='<td>'+ value.work_title + '</td>';

                    var isChecked = value.status == '1' ? 'checked' : '';
                    var statusText = value.status == '1' ? '<span class="text-success">Completed</span>' : '<span class="text-danger">Un-completed</span>';

                    html += '<td>' +
                      '<input type="checkbox" id="mySwitch_' + value.id + '" onclick=\'actionUpdate({action:"status", row:' + JSON.stringify(value) +'})\' ' + isChecked + '>' +
                      '<label class="form-check-label" for="mySwitch_' + value.id + '" id="show_status_' + value.id + '">' + statusText + '</label>' +
                      '</td>';

                    html += '<td> <input type="button" value="Delete" class="btn btn-danger" onclick=\'actionUpdate({action:"delete", row:' + JSON.stringify(value) +'})\'></td>';
                  html +='</tr>';
                });
                $('#updated_Data').html(html);
              }
            }, error: function(response) {
                console.log('Error:', response);
            }
        });
    }
    
  };
  $(document).keypress(function(event){
  
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
      var workVal = $("#work").val();
      if (workVal!="") {
        var formData = {
            work_title: workVal,
            status: 0,
            _token: '{{ csrf_token() }}'
        };
        $.ajax({
            url: "{{ route('todos.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
              if(response['status']==false){ // validation
                $("#error").text(response['validation']['work_title']);
              }else{
                $('#alert').slideDown(300);
                $("#message").text(response['message']);
                let html = '';

                $.each(response['data'], function(key, value){
                  html +='<tr>';
                    html +='<td>'+ parseInt(key+1) +'</td>';
                    html +='<td>'+ value.work_title + '</td>';

                    var isChecked = value.status == '1' ? 'checked' : '';
                    var statusText = value.status == '1' ? '<span class="text-success">Completed</span>' : '<span class="text-danger">Un-completed</span>';

                    html += '<td>' +
                      '<input type="checkbox" id="mySwitch_' + value.id + '" onclick=\'actionUpdate({action:"status", row:' + JSON.stringify(value) +'})\' ' + isChecked + '>' +
                      '<label class="form-check-label" for="mySwitch_' + value.id + '" id="show_status_' + value.id + '">' + statusText + '</label>' +
                      '</td>';

                    html += '<td> <input type="button" value="Delete" class="btn btn-danger" onclick=\'actionUpdate({action:"delete", row:' + JSON.stringify(value) +'})\'></td>';
                  html +='</tr>';
                });
                $('#updated_Data').html(html);
                $("#work").val('');
              }
            },
            error: function(response) {
                console.log('Error:', response);
            }
        });

      }else{
        alert("Please! Enter your work");
        $("#work").focus();
      }
    }
    
  });
</script>
