/* The consent form. The PDF is built here, in the browser, from the same words
   that were on screen - then posted once and emailed. Nothing is stored on the
   server, which is the promise the form itself makes. */
(function () {
  "use strict";

  var root = document.getElementById("samtykke");

  if (!root || typeof SAMTYKKE === "undefined") {
    return;
  }

  var LANG = SAMTYKKE.lang === "en" ? "en" : "da";
  var T = SAMTYKKE.text[LANG];

  function $(id) { return document.getElementById("sam-" + id); }

  // ------------------------------------------------------------ the words
  function fill() {
    T = SAMTYKKE.text[LANG];

    $("title").textContent = T.title;
    $("who").textContent = T.who;
    $("intro").textContent = T.intro;

    var terms = $("terms");
    terms.innerHTML = "";
    T.terms.forEach(function (p) {
      var e = document.createElement("p");
      e.textContent = p;
      terms.appendChild(e);
    });

    // Ticks survive a language switch: the person answered a question, not a
    // sentence in one particular language.
    var kept = {};
    root.querySelectorAll("#sam-uses input").forEach(function (i) { kept[i.dataset.key] = i.checked; });

    var uses = $("uses");
    uses.innerHTML = "";
    T.uses.forEach(function (pair) {
      var wrap = document.createElement("label");
      wrap.className = "samtykke-tick";
      var box = document.createElement("input");
      box.type = "checkbox";
      box.dataset.key = pair[0];
      if (kept[pair[0]]) { box.checked = true; }
      var span = document.createElement("span");
      span.textContent = pair[1];
      wrap.appendChild(box);
      wrap.appendChild(span);
      uses.appendChild(wrap);
    });

    // Each label is its own element and set with textContent. Writing into a
    // stray text node is how four of these came out blank last time.
    [["hUses", "hUses"], ["usesHelp", "usesHelp"], ["hYou", "hYou"],
     ["lName", "name"], ["lBirth", "birth"], ["lEmail", "email"], ["lPhone", "phone"],
     ["hGuardian", "hGuardian"], ["guardianWhy", "guardianWhy"],
     ["lGName", "gname"], ["lGRel", "grel"],
     ["hSign", "hSign"], ["signHelp", "signHelp"],
     ["padHint", "padHint"], ["gpadHint", "gpadHint"],
     ["agreeText", "agree"]
    ].forEach(function (pair) {
      var el = $(pair[0]);
      if (el) { el.textContent = T[pair[1]]; }
    });

    $("clear").textContent = T.clear;
    $("gclear").textContent = T.clear;
    $("send").textContent = T.send;

    root.querySelectorAll(".samtykke-langbtn").forEach(function (b) {
      b.setAttribute("aria-pressed", b.dataset.lang === LANG ? "true" : "false");
    });

    $("err").hidden = true;
  }

  root.querySelectorAll(".samtykke-langbtn").forEach(function (b) {
    b.addEventListener("click", function () { LANG = b.dataset.lang; fill(); });
  });

  // --------------------------------------------------------- signature pad
  function makePad(canvas) {
    var ctx = canvas.getContext("2d");
    var state = { inked: false };
    var drawing = false, last = null;

    function size() {
      // Draw at the device's real resolution, or a finger-drawn line lands in
      // the PDF as a blurry staircase.
      var ratio = window.devicePixelRatio || 1;
      var box = canvas.getBoundingClientRect();
      var keep = state.inked ? canvas.toDataURL() : null;
      canvas.width = Math.round(box.width * ratio);
      canvas.height = Math.round(box.height * ratio);
      ctx.scale(ratio, ratio);
      ctx.lineWidth = 2.2;
      ctx.lineCap = "round";
      ctx.lineJoin = "round";
      ctx.strokeStyle = "#111827";
      if (keep) {
        var img = new Image();
        img.onload = function () { ctx.drawImage(img, 0, 0, box.width, box.height); };
        img.src = keep;
      }
    }

    function at(ev) {
      var box = canvas.getBoundingClientRect();
      return { x: ev.clientX - box.left, y: ev.clientY - box.top };
    }

    canvas.addEventListener("pointerdown", function (ev) {
      ev.preventDefault();
      canvas.setPointerCapture(ev.pointerId);
      drawing = true;
      state.inked = true;
      last = at(ev);
      ctx.beginPath();
      ctx.moveTo(last.x, last.y);
      ctx.lineTo(last.x + 0.1, last.y);
      ctx.stroke();
    });

    canvas.addEventListener("pointermove", function (ev) {
      if (!drawing) { return; }
      ev.preventDefault();
      var p = at(ev);
      ctx.beginPath();
      ctx.moveTo(last.x, last.y);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
      last = p;
    });

    ["pointerup", "pointercancel", "pointerleave"].forEach(function (e) {
      canvas.addEventListener(e, function () { drawing = false; });
    });

    window.addEventListener("resize", size);

    state.size = size;
    state.clear = function () { ctx.clearRect(0, 0, canvas.width, canvas.height); state.inked = false; };
    state.data = function () { return canvas.toDataURL("image/png"); };

    return state;
  }

  var sig = makePad($("pad"));
  var gsig = makePad($("gpad"));
  $("clear").addEventListener("click", function () { sig.clear(); });
  $("gclear").addEventListener("click", function () { gsig.clear(); });

  // ------------------------------------------------------------- the PDF
  function stamp() {
    var d = new Date();
    function p(n) { return String(n).padStart(2, "0"); }
    return p(d.getDate()) + "-" + p(d.getMonth() + 1) + "-" + d.getFullYear() +
           " " + p(d.getHours()) + ":" + p(d.getMinutes());
  }

  function buildPdf(name, when) {
    var jsPDF = window.jspdf.jsPDF;
    var doc = new jsPDF({ unit: "mm", format: "a4" });
    var M = 18, W = 210 - M * 2;
    var y = M;

    function room(need) { if (y + need > 278) { doc.addPage(); y = M; } }

    doc.setFont("helvetica", "bold");
    doc.setFontSize(15);
    doc.text(T.title, M, y);
    y += 7;

    doc.setFont("helvetica", "normal");
    doc.setFontSize(9);
    doc.setTextColor(90);
    doc.splitTextToSize(T.who, W).forEach(function (l) { doc.text(l, M, y); y += 4.4; });
    y += 4;
    doc.setTextColor(20);
    doc.setFontSize(10);

    T.terms.forEach(function (p) {
      doc.splitTextToSize(p, W).forEach(function (l) { room(6); doc.text(l, M, y); y += 4.8; });
      y += 2.5;
    });

    y += 3;
    room(12);
    doc.setFont("helvetica", "bold");
    doc.text(T.pdfUses + ":", M, y);
    y += 5.5;
    doc.setFont("helvetica", "normal");

    root.querySelectorAll("#sam-uses input").forEach(function (b) {
      var line = (b.checked ? "[x] " : "[ ] ") + b.nextElementSibling.textContent +
                 "  -  " + (b.checked ? T.yes : T.no);
      doc.splitTextToSize(line, W).forEach(function (l) { room(6); doc.text(l, M, y); y += 4.8; });
      y += 1.6;
    });

    y += 4;
    room(30);
    doc.setFont("helvetica", "bold");
    doc.text(T.pdfPerson + ":", M, y);
    y += 5.5;
    doc.setFont("helvetica", "normal");

    [T.name + ": " + name,
     T.birth + ": " + ($("birth").value || "-"),
     T.email + ": " + ($("email").value.trim() || "-"),
     T.phone + ": " + ($("phone").value.trim() || "-")
    ].forEach(function (l) { room(6); doc.text(l, M, y); y += 5; });

    y += 3;
    room(48);
    doc.setFont("helvetica", "bold");
    doc.text(T.hSign + ":", M, y);
    y += 4;
    doc.setFont("helvetica", "normal");
    doc.addImage(sig.data(), "PNG", M, y, 78, 25);
    y += 27;
    doc.setDrawColor(150);
    doc.line(M, y, M + 78, y);
    y += 4.6;
    doc.setFontSize(9);
    doc.setTextColor(90);
    doc.text(name, M, y);
    y += 4.4;
    doc.text(T.pdfSigned + "  ·  " + T.pdfAt + ": " + when, M, y);
    y += 8;
    doc.setTextColor(20);
    doc.setFontSize(10);

    if (gsig.inked || $("gname").value.trim()) {
      room(46);
      doc.setFont("helvetica", "bold");
      doc.text(T.pdfGuardian + ":", M, y);
      y += 5.5;
      doc.setFont("helvetica", "normal");
      doc.text(T.gname + ": " + ($("gname").value.trim() || "-"), M, y);
      y += 5;
      doc.text(T.grel + ": " + ($("grel").value.trim() || "-"), M, y);
      y += 5;

      if (gsig.inked) {
        doc.addImage(gsig.data(), "PNG", M, y, 78, 25);
        y += 27;
        doc.setDrawColor(150);
        doc.line(M, y, M + 78, y);
        y += 4.6;
        doc.setFontSize(9);
        doc.setTextColor(90);
        doc.text($("gname").value.trim() || "-", M, y);
        y += 4.4;
        doc.text(T.pdfSigned + "  ·  " + T.pdfAt + ": " + when, M, y);
      }
    }

    return doc;
  }

  // --------------------------------------------------------------- send
  function fail(message) {
    var err = $("err");
    err.textContent = message;
    err.hidden = false;
    err.scrollIntoView({ block: "nearest" });
  }

  $("send").addEventListener("click", function () {
    var name = $("name").value.trim();
    var email = $("email").value.trim();
    var boxes = [].slice.call(root.querySelectorAll("#sam-uses input"));
    var main = boxes.filter(function (b) { return b.dataset.key === "all"; })[0];
    var guardianStarted = $("gname").value.trim() !== "" || gsig.inked;

    if (!name) { return fail(T.errName); }
    if (!main || !main.checked) { return fail(T.errUse); }
    if (!sig.inked) { return fail(T.errSign); }
    if (!$("agree").checked) { return fail(T.errAgree); }
    if (guardianStarted && !gsig.inked) { return fail(T.errGuardianSign); }
    // Unlike the offline file, this one emails the person their copy - so
    // without an address there is nothing to send it to.
    if (SAMTYKKE.copy && !/.+@.+\..+/.test(email)) { return fail(T.errEmailCopy); }

    $("err").hidden = true;
    $("send").disabled = true;
    $("send").textContent = T.sending;

    var doc = buildPdf(name, stamp());
    var body = new FormData();
    body.append("action", "samtykke_send");
    body.append("nonce", SAMTYKKE.nonce);
    body.append("lang", LANG);
    body.append("name", name);
    body.append("email", email);
    body.append("website", $("website").value);
    body.append("pdf", doc.output("datauristring"));

    fetch(SAMTYKKE.ajax, { method: "POST", body: body, credentials: "same-origin" })
      .then(function (r) { return r.json().catch(function () { return { success: false }; }); })
      .then(function (data) {
        if (!data || !data.success) { throw new Error("send failed"); }
        $("done").textContent = (data.data && data.data.copied) ? T.done : T.doneNoCopy;
        $("done").hidden = false;
        $("send").textContent = T.send;
        $("done").scrollIntoView({ block: "nearest" });
      })
      .catch(function () {
        $("send").disabled = false;
        $("send").textContent = T.send;
        fail(T.errSend);
      });
  });

  fill();
  sig.size();
  gsig.size();
}());
