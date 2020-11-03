<extends:layout.base title="[[Welcome to the Arena]]" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<stack:push name="scripts">
    <script src="/scripts/main.js"></script>
</stack:push>

<define:body>
    <div style="font-size: 12px; margin-top: 10px;">
        [[This view file is located in]] <b>app/views/home.dark.php</b> [[and rendered by]] <b>Controller\HomeController</b>.
    </div>
</define:body>