function search_request(){
    $.ajax({
        url: "get_data.php",
        method: "POST",
        data: {
            "search_bar_content" : document.getElementById("search_bar").value
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (data) {
            console.log(data);
        }
    });
};

