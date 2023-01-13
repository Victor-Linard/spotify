<div>
    <script src="https://open.spotify.com/embed-podcast/iframe-api/v1" async></script>
    <div id="embed-iframe"></div>

    <script>
        window.onSpotifyIframeApiReady = (IFrameAPI) => {
            let element = document.getElementById('embed-iframe');
            let options = {
                width: '250px',
                height: '150px',
                uri: 'spotify:track:0SKNkEMV313mE6oYsgVCgm'
            };
            let callback = (EmbedController) => {};
            IFrameAPI.createController(element, options, callback);
        };
    </script>
</div