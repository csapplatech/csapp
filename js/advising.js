$(document).ready(function() {
   $('.clickMe').click(function() {
           ($(this).next()).toggle();
   }); 
   $(".button").on("click", function(){      
        var butID=$(this).attr('id');
        if($(this).text()=="Add")
        {
            $(this).text("Add Alt");
            var tableID="row"+butID;
            cellAid="#a"+butID;
            cellA =$(cellAid).text();
            cellBid="#b"+butID;
            cellB =$(cellBid).text();
            cellCid="#c"+butID;
            cellC =$(cellCid).text();
            cellDid="#d"+butID;
            cellD =$(cellDid).text();
            cellEid="#e"+butID;
            cellE =$(cellEid).text();
            $('#target').append("<tr id='"+tableID+"'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td>"+cellE+"</td></tr>");
        }
        else if($(this).text()=="Add Alt")
        {
            var removeID ="#row"+butID;
            $(removeID).remove();
            $(this).text("Remove");
            var tableID="row"+butID;
            cellA =$(cellAid).text();
            cellBid="#b"+butID;
            cellB =$(cellBid).text();
            cellCid="#c"+butID;
            cellC =$(cellCid).text();
            cellDid="#d"+butID;
            cellD =$(cellDid).text();
            cellEid="#e"+butID;
            cellE =$(cellEid).text();
            $('#altTable').append("<tr id='"+tableID+"'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td>"+cellE+"</td></tr>");
        } else {
            var removeID ="#row"+butID;
            $(this).text("Add");
            $(removeID).remove();
        }
    });
    $('#reset').click(function() {
        window.location.reload();
   }); 
   
    $('#save').click(function() {
        window.location.reload();
   });
});