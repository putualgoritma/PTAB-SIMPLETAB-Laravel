<!DOCTYPE html>
<html>
<head>
    <title>How to Use Fullcalendar in Laravel 8</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="
https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js
"></script>
<link href="
https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css
" rel="stylesheet">
</head>
<body>
  
<div class="container">
    <br />
    <h1 class="text-center text-primary"><u>How to Use Fullcalendar in Laravel 8</u></h1>
    <br />

    <div id="calendar"></div>

</div>
   
<script>
function myFunction(data) {
    swal.close()
    if(confirm("Are you sure you want to remove it?"))
            {
                $.ajax({
                    url:'{{ route("admin.duty.action") }}',
                    type:"POST",
                    data:{
                        id:data,
                        type:"delete"
                    },
                    success:function(response)
                    {
                        location.reload();
                        // calendar.fullCalendar('refetchEvents');
                        // alert("Event Deleted Successfully");
                    }
                })
            }
}
$(document).ready(function () {

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#calendar').fullCalendar({
        editable:false,
        eventStartEditable: false,
        disableDragging: true,
        displayEventTime: false,
        header:{
            left:'prev,next today',
            center:'title',
            right:'month'
        },
        events:'{{ route("admin.duty.index") }}',
        selectable:true,
        
        selectHelper: true,
        select: async function (start, end, allDay) {
	  const { value: formValues } = await Swal.fire({
		title: 'Add Event',
		html:
		  '<input id="swalEvtTitle" class="swal2-input" placeholder="Enter title">' +
		  '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description"></textarea>' +
		  '<input id="title" class="swal2-input" placeholder="Enter URL">'+
          '<select id="staff" class="swal2-input" placeholder="Enter URL">'+
            '@foreach ($pgw as $data)'+
            '<option value="{{$data->id}}"> {{$data->name}} </option>'+  
           '@endforeach'+
            
            '</select>',
		focusConfirm: false,
		preConfirm: () => {
		  return [
			document.getElementById('swalEvtTitle').value,
			document.getElementById('swalEvtDesc').value,
			document.getElementById('title').value,
            document.getElementById('staff').value,
		  ]
		}
	  });

            // var title = prompt('Event Title:');

    //   	const { value: formValues } = await Swal.fire({
	// 	title: 'Add Event',
	// 	html:
	// 	  '<input id="swalEvtTitle" class="swal2-input" placeholder="Enter title">' +
	// 	  '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description"></textarea>' +
	// 	  '<input id="swalEvtURL" class="swal2-input" placeholder="Enter URL">',
	// 	focusConfirm: false,
	// 	preConfirm: () => {
	// 	  return [
	// 		document.getElementById('swalEvtTitle').value,
	// 		document.getElementById('swalEvtDesc').value,
	// 		document.getElementById('swalEvtURL').value
	// 	  ]
	// 	}
	//   });
    console.log(""+document.getElementById('staff').value)
            if(formValues)
            {
                var start = $.fullCalendar.formatDate(start, 'Y-MM-DD HH:mm:ss');

                var end = $.fullCalendar.formatDate(end, 'Y-MM-DD HH:mm:ss');

                $.ajax({
                    url:'{{ route("admin.duty.action") }}',
                    type:"POST",
                    data:{
                        staff_id : document.getElementById('staff').value,
                        title: "tesss",
                        start: start,
                        end: end,
                        type: 'add'
                    },
                    success:function(data)
                    {
                        console.log(data)
                        if(data == "fail" ){
                        alert("Data sudah ada");
                    }
                    else{
                        calendar.fullCalendar('refetchEvents');
                        alert("Event Created Successfully");
                    }
                    }
                })
            }
        },
        editable:true,
        eventResize: function(event, delta)
        {
            var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
            var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
            var title = event.title;
            var id = event.id;
            $.ajax({
                url:'{{ route("admin.duty.action") }}',
                type:"POST",
                data:{
                    title: title,
                    start: start,
                    end: end,
                    id: id,
                    type: 'update'
                },
                success:function(response)
                {
                    calendar.fullCalendar('refetchEvents');
                    alert("Event Updated Successfully");
                }
            })
        },
        eventDrop: function(event, delta)
        {
            var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD HH:mm:ss');
            var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD HH:mm:ss');
            var title = event.title;
            var id = event.id;
            $.ajax({
                url:'{{ route("admin.duty.action") }}',
                type:"POST",
                data:{
                    title: title,
                    start: start,
                    end: end,
                    id: id,
                    type: 'update'
                },
                success:function(response)
                {
                    calendar.fullCalendar('refetchEvents');
                    alert("Event Updated Successfully");
                }
            })
        },

        eventClick:function(details)
        {
            var id = details.id;
            var t = 3;
            console.log(details)
            $.ajax({
                    url:'{{ route("admin.duty.edit1") }}',
                    type:"GET",
                    data:{
                        id: id,
                    },
                    success:function(data)
                    {
                        console.log(data)
                        Swal.fire({
		title: 'Add Event',
		html:
		  data[1].title +data[1].user_id+t+
		  '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description">'+parseInt(t)+'</textarea>' +
		  '<input id="title" class="swal2-input" placeholder="Enter URL">'+
          '<select id="staff" class="swal2-input" placeholder="Enter URL">'+
        //     '@foreach ($pgw as $data)'+
        //     '<option @if($data->id=='++') selected @endif value="{{$data->id}}"> {{$data->id}} </option>'+  
        //    '@endforeach'+
            // data[0]
            data[0]+
            '</select>'+
            '<button onclick="myFunction('+id+')">Click me</button>'
            ,
		focusConfirm: false,
		preConfirm: () => {
		  return [
			document.getElementById('swalEvtTitle').value,
			document.getElementById('swalEvtDesc').value,
			document.getElementById('title').value,
            document.getElementById('staff').value,
		  ]
		}
	  });
                    }
                })
                
   
            // if(confirm("Are you sure you want to remove it?"))
            // {
            //     var id = event.id;
            //     $.ajax({
            //         url:'{{ route("admin.duty.action") }}',
            //         type:"POST",
            //         data:{
            //             id:id,
            //             type:"delete"
            //         },
            //         success:function(response)
            //         {
            //             calendar.fullCalendar('refetchEvents');
            //             alert("Event Deleted Successfully");
            //         }
            //     })
            // }
        }
    });

});
  
</script>
  
</body>
</html>
