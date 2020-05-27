$(document).ready(function(){

    $("#projects-name").change(function(){
        alert("1");
        var project_id = $(this).val();

        $.ajax({
            type: "GET",
            url: "http://10.0.0.130/zcplm_developing/frontend/web/project-select/get-project",
            data: {project_id:project_id, name:name},
            success: function(data) {
                if (data != "null") {
                    var data = $.parseJSON(data);
                    alert("2:"+data.name);
                }
                else {
                    alert("3");
                }
            },
            timeout: 3000,
            error: function(){
                alert("4");
            }
        });
    });
}