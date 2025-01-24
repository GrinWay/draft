vcl 4.0;

backend default {
    .host = "127.0.0.1";
    .port = "8000";
}

acl invalidators {
    "localhost";
    "127.0.0.1";
    "0.0.0.0";
}

sub vcl_recv {
    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }

    set req.http.Surrogate-Capability = "abc=ESI/1.0";

    if (req.method == "PURGE") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }
        return (purge);
    }

    if (req.http.Cache-Control ~ "no-cache" && client.ip ~ invalidators) {
        set req.hash_always_miss = true;
    }

    if (req.method == "BAN") {
        if (!client.ip ~ invalidators) {
            return (synth(405, "Not allowed"));
        }

        if (req.http.X-Cache-Tags) {
            ban("obj.http.X-Host ~ " + req.http.X-Host
                + " && obj.http.X-Url ~ " + req.http.X-Url
                + " && obj.http.content-type ~ " + req.http.X-Content-Type
                + " && obj.http.X-Cache-Tags ~ " + req.http.X-Cache-Tags
            );
        } else {
            ban("obj.http.X-Host ~ " + req.http.X-Host
                + " && obj.http.X-Url ~ " + req.http.X-Url
                + " && obj.http.content-type ~ " + req.http.X-Content-Type
            );
        }

        return (synth(200, "Banned"));
    }
}

sub vcl_backend_response {
    set beresp.http.X-Url = bereq.url;
    set beresp.http.X-Host = bereq.http.host;

    if (beresp.http.Surrogate-Control ~ "ESI/1.0") {
        unset beresp.http.Surrogate-Control;
        set beresp.do_esi = true;
    }
}

sub vcl_deliver {
    if (!resp.http.X-Cache-Debug) {
        unset resp.http.X-Url;
        unset resp.http.X-Host;
        unset resp.http.X-Cache-Tags;
    }
}
