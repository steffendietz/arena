<extends:layout.base title="[[Welcome to the Arena]]" />
<use:element path="embed/links" as="homepage:links" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<stack:push name="scripts">
    <script src="/scripts/main.js"></script>
</stack:push>

<define:body>
    <div class="wrapper">
        <div class="placeholder">
            <img src="/images/logo.svg" alt="Framework Logotype" width="200px" />
            <h2>[[Welcome to the Arena]] (@actor) (@actorUuid)</h2>
            <script>
            var userUuid = @actorUuidJs;
            </script>

            <homepage:links />

            <div style="font-size: 12px; margin-top: 10px;">
                [[This view file is located in]] <b>app/views/home.dark.php</b> [[and rendered by]] <b>Controller\HomeController</b>.
            </div>
        </div>
    </div>
</define:body>