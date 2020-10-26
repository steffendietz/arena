<extends:layout.base title="[[Login]]"/>
<use:element path="embed/links" as="homepage:links"/>

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css"/>
</stack:push>

<define:body>
    <div class="wrapper">
        <div class="placeholder">
            <img src="/images/logo.svg" alt="Framework Logotype" width="200px"/>
            <h2>[[Welcome to the Arena]]</h2>

            <homepage:links git="https://github.com/spiral/app" style="font-weight: bold;"/>

            <h3>[[Login]]</h3>
            <div style="font-size: 12px; margin-top: 10px;">
            <form action="/authentication/login" method="post">
                <input name="username" type="text" placeholder="Username"/>
                <input name="password" type="text" placeholder="Password"/>
                <input type="submit" value="[[Submit]]"/>
            </form>
            </div>
        </div>
    </div>
</define:body>

