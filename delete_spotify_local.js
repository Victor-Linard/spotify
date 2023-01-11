function delete_single_row(table, id) {
    $.ajax({
        url: "../wp-content/plugins/ld_spotify/delete_spotify_local.php",
        method: "POST",
        data: {
            "mode": "single",
            "table" : table,
            "id": id
        },
        async: false,
        success: function (data) {
            console.log(data);
            location.reload();
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function delete_all_rows(table) {
    $.ajax({
        url: "../wp-content/plugins/ld_spotify/delete_spotify_local.php",
        method: "POST",
        data: {
            "mode": "all",
            "table" : table
        },
        async: false,
        success: function (data) {
            console.log(data);
            location.reload();
        },
        error: function (data) {
            console.log(data);
        }
    });
}