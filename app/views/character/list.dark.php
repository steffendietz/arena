<extends:layout.base title="[[Character List]]" />

<stack:push name="styles">
    <link rel="stylesheet" href="/styles/welcome.css" />
</stack:push>

<define:body>
    @foreach($characters as $character)
    <div>
        <span class="uuid">{{ $character->getUuid() }}</span> -
        <a href="@route('searchmatch', ['characterUuid' => $character->getUuid()])">
            {{ $character->getName() }}
        </a>
        <?php if($character->isMatchSearching()): ?>
            <span>searching</span>
        <?php endif; ?>
    </div>
    @endforeach
</define:body>