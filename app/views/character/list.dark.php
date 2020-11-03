<extends:layout.base title="[[Character List]]" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<define:body>
    @foreach($characters as $character)
    <div>
        {{ $character->getUuid() }} - {{ $character->getName() }}
    </div>
    @endforeach
</define:body>