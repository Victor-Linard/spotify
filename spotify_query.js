function search_request($param=null){
    $.ajax({
        url: "../../../../wp-content/plugins/ld_spotify/includes/get_data.php",
        method: "POST",
        data: {
            "search_bar_content" : document.getElementById("search_bar_spotify").value,
            "reload" : $param
        },
        async: false,
        success: function (data) {
            document.getElementById("results").innerHTML=data;
        },
        error: function (data) {
            console.log(data);
        }
    });
}