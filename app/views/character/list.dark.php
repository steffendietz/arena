<extends:layout.base title="[[Character List]]" />

<use:element path="embed/links" as="homepage:links" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<define:body>
    <div class="wrapper">
        <div class="placeholder">
            <img src="/images/logo.svg" alt="Framework Logotype" width="200px" />
            <h2>[[Welcome to the Arena]]</h2>

            <homepage:links />

            @foreach($characters as $character)
            <div>
                {{ $character->getName() }}
            </div>
            @endforeach
        </div>
    </div>
</define:body>