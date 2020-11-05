<a href="/">[[Home]]</a>
| <a href="@route('arena:generate')">[[Generate Arena]]</a>
@ifloggedin
| <a href="@route('character:list')">[[Character List]]</a>
| <a href="@route('character')">[[Generate Character]]</a>
| <a href="@route('authentication:logout')">[[Logout]]</a>
@else
| <a href="@route('authentication:index')">[[Login]]</a>
@endif
| <a href="http://localhost:2112/metrics">[[Application Metrics]]</a>