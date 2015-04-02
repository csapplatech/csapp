$(document).ready(function() {
   $('.clickMe').click(function() {
       console.log("blah");
           ($(this).next()).toggle();
   }); 
   $(".button").on("click", function(){      
    var butID=$(this).attr('id');
    if($(this).text()=="Add")
    {
        $(this).text("Add Alt");
        var tableID="row"+butID;
        $('#target').append("<tr id='"+tableID+"'> <td>Eve</td><td>Jackson</td> <td></td><td>94</td>"+tableID+"<td>"+$(this).attr('id')+"</td><td></td></tr>");
    }
    else if($(this).text()=="Add Alt")
    {
        var removeID ="#row"+butID;
        $(removeID).remove();
        $(this).text("Remove");
        var tableID="row"+butID;
        $('#altTable').append("<tr id='"+tableID+"'> <td >Eve</td><td>Jackson</td> <td></td><td>94</td>"+tableID+"<td>"+$(this).attr('id')+"</td><td></td></tr>");
    } else {
        var removeID ="#row"+butID;
        $(this).text("Add");
        $(removeID).remove();
    }
    });
});