<a href="/ping.html">[[Create Queue Task]]</a>
| <a href="@route('arena:generate')">[[Generate Arena]]</a>
| <a href="@route('character')">[[Generate Character]]</a>
@ifloggedin
| <a href="@route('authentication:logout')">[[Logout]]</a>
@else
| <a href="@route('authentication:index')">[[Login]]</a>
@endif
| <a href="http://localhost:2112/metrics">[[Application Metrics]]</a>