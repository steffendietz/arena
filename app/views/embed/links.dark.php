<a href="/">[[Home]]</a>
@ifloggedin
| <a href="@route('api.list', ['controller' => 'character'])">[[Character List]]</a>
| <a href="@route('characterGenerate')">[[Generate Character]]</a>
| <a href="@route('authentication:logout')">[[Logout]]</a>
@else
| <a href="@route('authentication:index')">[[Login]]</a>
@endif