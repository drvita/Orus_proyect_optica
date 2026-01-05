<!DOCTYPE html>
@php
    $ruta = 'static/js/';
@endphp
<html lang="es">

<head>
    <meta charset="utf-8" />
    <link rel="icon" href="/img/logo_24.png" />
    <meta name="theme-color" content="#3c8dbc" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="apple-touch-icon" href="/img/logo_24.png" sizes="24x24" />
    <link rel="manifest" href="/manifest.json" />
    <script async src="https://unpkg.com/pwacompat" crossorigin="anonymous"></script>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css" />
    <!-- iCheck -->
    <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
    <!-- JQVMap -->
    <link rel="stylesheet" href="/plugins/jqvmap/jqvmap.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/adminlte.min.css" />
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css" />
    <!-- summernote -->
    <link rel="stylesheet" href="/plugins/summernote/summernote-bs4.css" />

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" />

    <!-- Datapicker -->
    <link rel="stylesheet" href="/css/main.css" />
    <link href="/static/css/main.0ac14d5d.css" rel="stylesheet">

    <!-- Main -->
    <link rel="stylesheet" href="/css/main.css" />
    @if (is_dir($ruta))
        @php
            $gestor = opendir($ruta);
        @endphp

        @while (($archivo = readdir($gestor)) !== false)
            @if (preg_match("/.+\.js$/im", $archivo))
                @php
                    $file = $ruta . $archivo;
                    $time = date('Ymd', filectime($file));
                @endphp
                <title>Orus || {{ $time }} </title>
            @endif
        @endwhile

    @endif

</head>

<body class="sidebar-mini sidebar-collapse text-sm" id="body">
    <noscript>Habilie javascript para cargar el sistema</noscript>
    <div id="root"></div>
    <iframe id="ifmcontentstoprint" title="iframe" style="width: 0px; height: 0px;"
        class="d-none d-print-block"></iframe>
    <!-- REQUIRED SCRIPTS -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwA3PMkKIB0VTVnf0-xGEe05dDIlsUPWc&libraries=places&language=es">
    </script>
    <!-- jQuery -->
    <script src="/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <!-- Alert -->
    <script src="/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- Main App -->
    <script src="/js/main.js"></script>
    <!-- Load File to React JS -->
    <script>
        ! function(e) {
            function r(r) {
                for (var n, l, f = r[0], i = r[1], a = r[2], c = 0, s = []; c < f.length; c++) l = f[c], Object.prototype
                    .hasOwnProperty.call(o, l) && o[l] && s.push(o[l][0]), o[l] = 0;
                for (n in i) Object.prototype.hasOwnProperty.call(i, n) && (e[n] = i[n]);
                for (p && p(r); s.length;) s.shift()();
                return u.push.apply(u, a || []), t()
            }

            function t() {
                for (var e, r = 0; r < u.length; r++) {
                    for (var t = u[r], n = !0, f = 1; f < t.length; f++) {
                        var i = t[f];
                        0 !== o[i] && (n = !1)
                    }
                    n && (u.splice(r--, 1), e = l(l.s = t[0]))
                }
                return e
            }
            var n = {},
                o = {
                    1: 0
                },
                u = [];

            function l(r) {
                if (n[r]) return n[r].exports;
                var t = n[r] = {
                    i: r,
                    l: !1,
                    exports: {}
                };
                return e[r].call(t.exports, t, t.exports, l), t.l = !0, t.exports
            }
            l.m = e, l.c = n, l.d = function(e, r, t) {
                l.o(e, r) || Object.defineProperty(e, r, {
                    enumerable: !0,
                    get: t
                })
            }, l.r = function(e) {
                "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {
                    value: "Module"
                }), Object.defineProperty(e, "__esModule", {
                    value: !0
                })
            }, l.t = function(e, r) {
                if (1 & r && (e = l(e)), 8 & r) return e;
                if (4 & r && "object" == typeof e && e && e.__esModule) return e;
                var t = Object.create(null);
                if (l.r(t), Object.defineProperty(t, "default", {
                        enumerable: !0,
                        value: e
                    }), 2 & r && "string" != typeof e)
                    for (var n in e) l.d(t, n, function(r) {
                        return e[r]
                    }.bind(null, n));
                return t
            }, l.n = function(e) {
                var r = e && e.__esModule ? function() {
                    return e.default
                } : function() {
                    return e
                };
                return l.d(r, "a", r), r
            }, l.o = function(e, r) {
                return Object.prototype.hasOwnProperty.call(e, r)
            }, l.p = "/";
            var f = this.webpackJsonporussystem = this.webpackJsonporussystem || [],
                i = f.push.bind(f);
            f.push = r, f = f.slice();
            for (var a = 0; a < f.length; a++) r(f[a]);
            var p = i;
            t()
        }([])
    </script>
    @if (is_dir($ruta))
        @php
            $gestor = opendir($ruta);
        @endphp

        @while (($archivo = readdir($gestor)) !== false)
            @if (preg_match("/.+\.js$/im", $archivo))
                @php
                    $file = $ruta . $archivo;
                    // Log::debug("{$file} : to add");
                @endphp
                <script src="/{{ $file }}"></script>
            @endif
        @endwhile

    @endif
</body>

</html>
