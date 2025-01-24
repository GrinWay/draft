vcl 4.1;

include "hit-miss.vcl";

import dynamic;
import std;

backend default none;

acl invalidators {
	"0.0.0.0"/0;
	"127.0.0.1";
	"localhost";
}

sub vcl_init {
        new dynamic_director = dynamic.director(whitelist = invalidators);
}

sub vcl_recv {
        # if VARNISH_BACKEND_HOST and VARNISH_BACKEND_PORT, use them to find a backend
        # if not, generate a synthetic response with vcl_synth
        if (std.getenv("VARNISH_BACKEND_HOST") && std.getenv("VARNISH_BACKEND_PORT")) {
                set req.backend_hint = dynamic_director.backend(std.getenv("VARNISH_BACKEND_HOST"), std.getenv("VARNISH_BACKEND_PORT"));
                # tweak the host header to match the backend's info
                if (std.getenv("VARNISH_BACKEND_PORT") == "80") {
                        set req.http.host = std.getenv("VARNISH_BACKEND_HOST");
                } else {
                        set req.http.host = std.getenv("VARNISH_BACKEND_HOST") + ":" + std.getenv("VARNISH_BACKEND_PORT");
                }
        } else {
                return(synth(200));
        }
}

sub vcl_synth {
        set resp.http.content-type = "text/html; charset=UTF-8;";
        synthetic("""<!DOCTYPE html>
                        <html><body>
                                <h1>Varnish is running!</h1>
""");

        if (std.getenv("VARNISH_BACKEND_HOST") || std.getenv("VARNISH_BACKEND_PORT")) {
                if (std.getenv("VARNISH_BACKEND_HOST")) {
                        synthetic("""<p>It appears you have set the <b>VARNISH_BACKEND_HOST</b> variable, and not <b>VARNISH_BACKEND_PORT</b>, but <b>Varnish needs both</b> to identify a backend</p>""");
                } else {
                        synthetic("""<p>It appears you have set the <b>VARNISH_BACKEND_PORT</b> variable, and not <b>VARNISH_BACKEND_HOST</b>, but <b>Varnish needs both</b> to identify a backend</p>""");
                }
        } else {
                synthetic("""<p>You now need to configure your backend. To do so, you can either:
<ul>
  <li>set both <b>VARNISH_BACKEND_HOST</b> and <b>VARNISH_BACKEND_PORT</b> environment variables</li>
  <li>edit/mount <b>/etc/varnish/default.vcl</b> with your routing and caching logic</li>
</ul>""");
        }
        synthetic("""</body></html>""");
        return (deliver);
}

sub vcl_backend_response {
}

include "verbose_builtin.vcl";

sub vcl_recv {
    # ...

    set req.http.cookie = ";" + req.http.cookie;
    set req.http.cookie = regsuball(req.http.cookie, "; +", ";");
    set req.http.cookie = regsuball(req.http.cookie, ";(PHPSESSID|REMEMBERME)=", "; \1=");
    set req.http.cookie = regsuball(req.http.cookie, ";[^ ][^;]*", "");
    set req.http.cookie = regsuball(req.http.cookie, "^[; ]+|[; ]+$", "");

    # ...
}
###
#
# https://foshttpcache.readthedocs.io/en/latest/varnish-configuration.html#varnish-configuration
#
# Choose to include:
#
# ls /etc/varnish/config/varnish -a
#
include "/etc/varnish/config/varnish/fos_purge.vcl";
sub vcl_recv {
    call fos_purge_recv;
}

include "/etc/varnish/config/varnish/fos_refresh.vcl";
sub vcl_recv {
    call fos_refresh_recv;
}

include "/etc/varnish/config/varnish/fos_ban.vcl";
sub vcl_recv {
    call fos_ban_recv;
}
sub vcl_backend_response {
    call fos_ban_backend_response;
}
sub vcl_deliver {
    call fos_ban_deliver;
}

include "/etc/varnish/config/varnish/fos_user_context.vcl";
include "/etc/varnish/config/varnish/fos_user_context_url.vcl";
sub vcl_recv {
    call fos_user_context_recv;
}
sub vcl_hash {
    call fos_user_context_hash;
}
sub vcl_backend_response {
    call fos_user_context_backend_response;
}
sub vcl_deliver {
    call fos_user_context_deliver;
}

include "/etc/varnish/config/varnish/fos_custom_ttl.vcl";
sub vcl_backend_response {
    call fos_custom_ttl_backend_response;
}

include "/etc/varnish/config/varnish/fos_debug.vcl";
sub vcl_deliver {
    call fos_debug_deliver;
}
###