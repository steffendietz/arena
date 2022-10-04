<!DOCTYPE html>
<html lang="@{locale}">

<head>
    <title>${title}</title>
    <block:head>
        <stack:collect name="styles" level="2" />
    </block:head>
</head>

<body>
    <div class="wrapper">
        <div class="placeholder">
            <h2>[[Welcome to the Arena]] (@actor) (@actorUuid)</h2>
            <script>
                var userUuid = @actorUuidJs;
            </script>

            <use:element path="embed/links" as="homepage:links" />
            <homepage:links />

            <div class="inner-wrapper">
                <block:body />
            </div>
        </div>
    </div>
    <div id="app"></div>
    <stack:collect name="scripts" level="1" />
</body>
<hidden>${context}</hidden>

</html>