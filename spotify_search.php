<!DOCTYPE html>
<html>
    <head>
        <title>Spotify Search</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script type="text/javascript" src="spotify_query.js"></script>
    </head>
    <label for="name">Search:</label>

    <input type="text" id="search_bar" name="search_bar" required
           minlength="1" maxlength="50" size="30">
    <button class="search" id="search" onclick="search_request()">Search</button>
</html>

