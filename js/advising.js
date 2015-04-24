var save_id = new Array();
var save_callnum = new Array();
var save_type = new Array(); //save type is alt or norm

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
            cellC =$(cellCid).text(); //cell C is call number
            //push both to arrays
            save_id.push(butID);
            save_callnum.push(cellC);
            save_type.push("norm");
            cellDid="#d"+butID;
            cellD =$(cellDid).text();
            cellEid="#e"+butID;
            cellE =$(cellEid).text();
            $('#target').append("<tr id='"+tableID+"' class='clicky'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td style=\"font-size: 90% \">"+cellE+"</td></tr>");
       }
        else if($(this).text()=="Add Alt")
        {
            save_type[save_id.indexOf(butID)]="alt";
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
            $('#altTable').append("<tr id='"+tableID+"' class='clicky'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td style=\"font-size: 90% \">"+cellE+"</td></tr>");
        } else {
            var index = save_id.indexOf(butID);
            save_id.splice(index,1);
            save_callnum.splice(index,1);
            save_type.splice(index,1);
            var removeID ="#row"+butID;
            $(this).text("Add");
            $(removeID).remove();
        }
    });
    $('#reset').on("click", function() {
        //window.location.reload()
        //test code
        //for(var i=0; i<save_id.length;i++ )
        //    console.log("ID:" +save_id[i] +" CallNum" +save_callnum[i] +" Type:"+ save_type[i]);
        /*var SendInfo = {
            Info: []
        };
        for (var i in save_type){
            SendInfo.Info.push(
                {
                    CallNumber: save_callnum[i] ,
                    Type: save_type[i] 
                });
        }
        console.log(JSON.stringify(SendInfo));*/
        /*var main = document.getElementById('#target');
        var alt = $('#altTable');
        for (var i = 0; i < main.rows.length; i++)
        {
            var id = main.rows[i].attr('id');
            var butID = id.substring(3);
            $('#'+butID).text("Add");
            $('#'+id).remove();
        }*/
    }); 
   
    $('#save').click(function() {
        var SendInfo = {
            Info: []
        };
        for (var i in save_type){
            SendInfo.Info.push(
                {
                    CallNumber: save_callnum[i] ,
                    Type: save_type[i] 
                });
        }
        $.ajax({
           url: rootURL + 'index.php/Advisingform/save',
           type: 'POST',
           //contentType : 'application/json',
           data: {data: JSON.stringify(SendInfo)},
           //data: {data: JSON.stringify(SendInfo)},
           success: function(data)
                {
                    alert('success!\n' + data);
                },
           error: function(data) 
                {
                    alert("failed!");
                }
        });
   });
   $("body").on("click", ".clicky", function(){      
        var tabID=$(this).attr('id');
        var butID=tabID.substring(3);
        if ($('#'+butID).text() == "Remove")
            remove(butID);
            
        else
            addALT(butID);
            
   });
   
});

function addMain(Mid) {
    $("#" + Mid).text("Add Alt");
        var butID =Mid;
        var tableID="row"+butID;
        cellAid="#a"+butID;
        cellA =$(cellAid).text();
        cellBid="#b"+butID;
        cellB =$(cellBid).text();
        cellCid="#c"+butID;
        cellC =$(cellCid).text(); //cell C is call number
        //push both to arrays
        save_id.push(butID);
        save_callnum.push(cellC);
        save_type.push("norm");
        cellDid="#d"+butID;
        cellD =$(cellDid).text();
        cellEid="#e"+butID;
        cellE =$(cellEid).text();
        $('#target').append("<tr id='"+tableID+"' class='clicky'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td style=\"font-size: 90% \">"+cellE+"</td></tr>");
}

function addALT(Aid) {
    var butID =Aid;
    save_type[save_id.indexOf(butID)]="alt";
    var removeID ="#row"+butID;
    $(removeID).remove();
    $("#"+Aid).text("Remove");
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
    $('#altTable').append("<tr id='"+tableID+"' class='clicky'> <td>"+cellA+"</td><td>"+cellB+"</td><td>"+cellC+"</td><td>"+cellD+"</td><td></td><td style=\"font-size: 90% \">"+cellE+"</td></tr>");
    $('#'+butID).text("Remove");
} 

function remove(Rid) {
    var butID = Rid;
    var index = save_id.indexOf(butID);
    save_id.splice(index,1);
    save_callnum.splice(index,1);
    save_type.splice(index,1);
    var removeID ="#row"+butID;
    //$(this).text("Add");
    $(removeID).remove();
    $('#'+butID).text("Add");
}
