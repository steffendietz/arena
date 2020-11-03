<extends:layout.base title="[[Login]]" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<define:body>
    <h3>[[Login]]</h3>
    <div style="font-size: 12px; margin-top: 10px;">
        <form action="/authentication/login" method="post">
            <input name="username" type="text" placeholder="[[Username]]" />
            <input name="password" type="password" placeholder="[[Password]]" />
            <input type="submit" value="[[Submit]]" />
        </form>
    </div>
</define:body>