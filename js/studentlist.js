$(document).ready(function() {
   $('.clickMe').click(function() { 
        var StudID=$(this).attr('id');
        
      console.log(StudID);
      $.ajax({
           url: 'loadStudentID',
           type: 'POST',
           data: { StudID: StudID},
           success: function(data)
                {
                    //alert('success!\n' + data);
                    //console.log(data);
                    window.location.href = "../Advisingform";
                },
           error: function(data) 
                {
                    alert("failed!");
                }
        });
   }); 
});