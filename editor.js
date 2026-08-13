// editor.js – DmM Split-View-Editor für Dateimeister
// Eingebunden wenn DM_Pfad gesetzt ist (index.php setzt die Variable).
(function () {
    'use strict';

    // ── Toolbar: DmM-Elemente ─────────────────────────────────────────────────
    var TB = [
        { z: "B",    t: "Fett",           v: "'''",     n: "/'''" },
        { z: "I",    t: "Kursiv",          v: "''",      n: "/''"  },
        { z: "Ü",    t: "Überschrift",     v: "<ue>",    n: "</ue>"   },
        { z: "Ü₀",   t: "Überschrift 0",   v: "<ue0>",   n: "</ue0>"  },
        { z: "«»",   t: "Graphem-Klammer", v: "<<",      n: ">>"      },
        { z: "rot",  t: "Rot",             v: "<rot>",   n: "</rot>"  },
        { z: "grün", t: "Grün",            v: "<gruen>", n: "</gruen>"},
        { z: "Anm",  t: "Anmerkung",       v: "<anm>",   n: "</anm>"  },
        { z: "–",    t: "Gedankenstrich",  v: "--",      n: ""        },
        { z: "¶",    t: "Absatz",          v: "\n\n",    n: ""        },
    ];

    // ── Kleine Hilfsfunktionen ────────────────────────────────────────────────
    function mk(tag, css) {
        var e = document.createElement(tag);
        if (css) e.style.cssText = css;
        return e;
    }

    function dmwrap(ta, vor, nach) {
        var s = ta.selectionStart, e = ta.selectionEnd, v = ta.value;
        ta.value = v.slice(0, s) + vor + v.slice(s, e) + nach + v.slice(e);
        ta.selectionStart = s + vor.length;
        ta.selectionEnd   = e + vor.length;
        ta.focus();
    }

    function ajax(methode, url, koerper, cb) {
        var x = new XMLHttpRequest();
        x.open(methode, url, true);
        if (methode === 'POST')
            x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        x.onload  = function () { cb(x.status, x.responseText, x); };
        x.onerror = function () { cb(0, 'Netzwerkfehler'); };
        x.send(koerper || null);
    }

    // ── Live-Vorschau (gedrosselt 350 ms) ────────────────────────────────────
    var pvTimer = null;
    function aktVorschau(ta, pv) {
        clearTimeout(pvTimer);
        pvTimer = setTimeout(function () {
            ajax('POST', '/vorschau.php',
                 'dmm=' + encodeURIComponent(ta.value),
                 function (st, txt) { if (st === 200) pv.innerHTML = txt; });
        }, 350);
    }

    // ── Editor-UI aufbauen und in Inhaltsspalte einsetzen ────────────────────
    function oeffneEditor(rohtext, schreibbar) {
        if (schreibbar === undefined) schreibbar = true;
        // Inhaltsspalte finden: breitestes <td>
        var tds = document.querySelectorAll('td'), zielTd = null, maxB = 0;
        for (var i = 0; i < tds.length; i++) {
            var b = tds[i].offsetWidth;
            if (b > maxB) { maxB = b; zielTd = tds[i]; }
        }
        if (!zielTd) { alert('Inhaltsspalte nicht gefunden.'); return; }
        zielTd.innerHTML = '';

        // Äußerer Wrapper
        var aussen = mk('div', 'width:100%;box-sizing:border-box;padding:4px');

        // Schreibschutz-Warnung
        if (!schreibbar) {
            var warn = mk('div',
                'background:#fdd;border:1px solid #c66;border-radius:4px;' +
                'padding:8px 12px;margin-bottom:8px;color:#900;font-weight:bold');
            warn.textContent = 'Diese Datei ist schreibgeschützt – Änderungen können hier nicht gesichert werden.';
            aussen.appendChild(warn);
        }

        // Toolbar
        var tbar = mk('div',
            'display:flex;flex-wrap:wrap;gap:3px;padding:5px 6px;' +
            'background:#d8d8ee;border:1px solid #aab;border-radius:4px;margin-bottom:6px');

        var ta = document.createElement('textarea');   // früh deklarieren für Closure

        TB.forEach(function (b) {
            var btn = mk('button');
            btn.type = 'button';
            btn.textContent = b.z;
            btn.title = b.t;
            btn.style.cssText =
                'padding:2px 9px;cursor:pointer;background:#fff;' +
                'border:1px solid #99a;border-radius:3px;font-size:13px;min-width:30px';
            btn.addEventListener('click', function () {
                dmwrap(ta, b.v, b.n);
                aktVorschau(ta, pv);
            });
            tbar.appendChild(btn);
        });

        // Split-Ansicht
        var split = mk('div', 'display:flex;gap:8px;height:62vh');

        // Linke Seite: Textarea (DmM-Quelltext)
        ta.value = rohtext;
        ta.style.cssText =
            'flex:1;min-width:0;font-family:monospace;font-size:14px;' +
            'padding:8px;border:1px solid #aab;resize:none;' +
            'background:#fff;box-sizing:border-box;line-height:1.5';
        ta.addEventListener('input', function () { aktVorschau(ta, pv); });

        // Rechte Seite: Live-Vorschau
        var pv = mk('div',
            'flex:1;min-width:0;overflow:auto;padding:8px;' +
            'border:1px solid #aab;background:#ffffd4;box-sizing:border-box;font-size:16px');

        split.appendChild(ta);
        split.appendChild(pv);

        // Statuszeile
        var stat = mk('div', 'margin-top:8px;display:flex;align-items:center;gap:10px');

        if (!schreibbar) { ta.readOnly = true; ta.style.background = '#f5f5f5'; }

        var btnS = mk('button');
        btnS.type = 'button'; btnS.textContent = '💾 Speichern';
        btnS.style.cssText =
            'padding:7px 22px;background:#446;color:#fff;' +
            'border:none;border-radius:4px;cursor:pointer;font-size:15px';
        if (!schreibbar) { btnS.disabled = true; btnS.style.opacity = '0.4'; }

        var btnA = mk('button');
        btnA.type = 'button'; btnA.textContent = 'Abbrechen';
        btnA.style.cssText =
            'padding:7px 16px;background:#888;color:#fff;' +
            'border:none;border-radius:4px;cursor:pointer;font-size:15px';

        var msg = mk('span', 'color:#446;font-size:14px');

        btnS.addEventListener('click', function () {
            btnS.disabled = true;
            msg.textContent = 'Speichere …';
            ajax('POST', '/speichereDatei.php',
                 'dmm=' + encodeURIComponent(ta.value) +
                 '&pfad=' + encodeURIComponent(window.DM_Pfad),
                 function (st, txt) {
                     if (st === 200) {
                         msg.textContent = '✓ Gespeichert – Seite wird neu geladen …';
                         setTimeout(function () { location.reload(); }, 1400);
                     } else {
                         msg.textContent = '✗ Fehler: ' + txt;
                         btnS.disabled = false;
                     }
                 });
        });

        btnA.addEventListener('click', function () { location.reload(); });

        stat.appendChild(btnS);
        stat.appendChild(btnA);
        stat.appendChild(msg);

        aussen.appendChild(tbar);
        aussen.appendChild(split);
        aussen.appendChild(stat);
        zielTd.appendChild(aussen);

        requestAnimationFrame(function () {
            ta.scrollTop = 0;
            ta.selectionStart = ta.selectionEnd = 0;
            ta.focus();
        });
        aktVorschau(ta, pv);   // initiale Vorschau
    }

    // ── Bearbeiten-Button auf der Seite einblenden ────────────────────────────
    function init() {
        if (!window.DM_Pfad) return;

        var btn = mk('button',
            'position:fixed;top:12px;right:12px;z-index:9999;' +
            'padding:6px 15px;background:#446;color:#fff;' +
            'border:none;border-radius:4px;cursor:pointer;font-size:14px;' +
            'opacity:0.88;box-shadow:0 2px 6px rgba(0,0,0,.3)');
        btn.type = 'button';
        btn.id   = 'dm-editbtn';
        btn.textContent = '✎ Bearbeiten';

        btn.addEventListener('click', function () {
            btn.textContent = '…';
            btn.disabled = true;
            ajax('GET',
                 '/holeDateiRoh.php?datei=' + encodeURIComponent(window.DM_Pfad),
                 null,
                 function (st, txt, xhr) {
                     if (st === 200) {
                         var schreibbar = !xhr || xhr.getResponseHeader('X-DM-Schreibbar') !== '0';
                         oeffneEditor(txt, schreibbar);
                     } else {
                         alert('Laden fehlgeschlagen:\n' + txt);
                         btn.textContent = '✎ Bearbeiten';
                         btn.disabled = false;
                     }
                 });
        });

        document.body.appendChild(btn);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
