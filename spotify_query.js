function search_request(){
    $.ajax({
        url: "../../../../wp-content/plugins/ld_spotify/get_data.php",
        method: "POST",
        data: {
            "search_bar_content" : document.getElementById("search_bar_spotify").value
        },
        async: false,
        success: function (data) {
            //console.log(data);
            document.getElementById("results").innerHTML=data;
        },
        error: function (data) {
            console.log(data);
        }
    });
};

