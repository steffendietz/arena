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
            <img src="/images/logo.svg" alt="Framework Logotype" width="200px" />
            <h2>[[Welcome to the Arena]] (@actor) (@actorUuid)</h2>
            <script>
                var userUuid = @actorUuidJs;
            </script>

            <use:element path="embed/links" as="homepage:links" />
            <homepage:links />

            <block:body />
        </div>
    </div>
    <stack:collect name="scripts" level="1" />
</body>
<hidden>${context}</hidden>

</html>