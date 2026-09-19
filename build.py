"""Build the two stand-alone consent files.

One template, two languages, so a change to the machinery cannot land in the
Danish file and miss the English one. Everything is inlined - jsPDF included -
because the finished file has to work as an email attachment or off a USB
stick with the wifi switched off. No server, nothing stored anywhere.
"""
import io
import json
import os

HERE = os.path.dirname(os.path.abspath(__file__))
JSPDF = open(os.path.join(HERE, "jspdf.umd.min.js"), encoding="utf-8").read()

DA = {
    "lang": "da",
    "file": "Samtykke-DANSK.html",
    "pdfname": "Samtykke",
    "editheader": """  TEKSTEN STÅR HERUNDER. Du må gerne rette i den.

  Ret kun det, der står mellem anførselstegnene "sådan her".
  Lad komma, klammer og anførselstegn stå, som de er - så virker filen
  fortsat. Gem filen bagefter, og åbn den i en browser for at se resultatet.

  Alt under "SLUT PÅ TEKSTEN" er maskineriet. Der er ingen grund til at røre
  det.""",
    "editend": "SLUT PÅ TEKSTEN. Herunder er maskineriet - lad det være.",
    "t": {
        "title": "Samtykke til brug af film og billeder",
        "who": "Dataansvarlig: Sportsværkstedet, Domhusgade 13, 1. sal, 6000 Kolding  ·  skriv@sportsvaerkstedet.dk",
        "intro": "Udfyld, sæt kryds ved det du siger ja til, og skriv under nederst.",
        "terms": [
            "Jeg giver Sportsværkstedet tilladelse til at optage video og tage billeder af mig i forbindelse med min behandling, og til at bruge materialet til markedsføring i de sammenhænge, jeg har sat kryds ved nedenfor.",
            "Jeg er indforstået med, at optagelserne kan vise min krop, mine skader og selve behandlingen, og at jeg kan være genkendelig.",
            "Samtykket er frivilligt. Jeg kan til enhver tid trække det tilbage ved at skrive til Sportsværkstedet. Herefter fjerner Sportsværkstedet materialet fra egne kanaler hurtigst muligt. Materiale, som andre allerede har delt eller hentet ned, kan ikke altid fjernes - det er jeg oplyst om.",
            "Jeg får en kopi af denne erklæring. Sportsværkstedet opbevarer den, så længe materialet er i brug, og i op til to år derefter.",
            "Jeg kan bede om indsigt i, rettelse af eller sletning af mine oplysninger, og jeg kan klage til Datatilsynet.",
        ],
        "hUses": "Hvor må materialet bruges?",
        "usesHelp": "Sæt kryds ved det, du siger ja til. Du bestemmer selv - du kan sige ja til noget og nej til resten.",
        "uses": [
            ["web", "Sportsværkstedets hjemmeside"],
            ["fb", "Facebook"],
            ["ig", "Instagram"],
            ["yt", "YouTube og TikTok"],
            ["ads", "Betalt annoncering (fx Facebook- og Google-annoncer)"],
            ["print", "Tryksager - brochurer, plakater, opslag"],
            ["other", "Anden markedsføring for Sportsværkstedet"],
            ["name", "Mit fornavn må nævnes sammen med materialet"],
        ],
        "hYou": "Dig",
        "name": "Navn", "birth": "Fødselsdato", "email": "E-mail",
        "phone": "Telefon (valgfrit)",
        "hGuardian": "Forælder eller værge",
        "guardianWhy": "Udfyldes kun, hvis den, der er filmet, er under 18 år. Forælder eller værge skriver under sammen med den unge.",
        "gname": "Forælder/værges navn", "grel": "Relation",
        "hSign": "Underskrift",
        "signHelp": "Skriv din underskrift med fingeren eller musen i feltet herunder.",
        "padHint": "Underskriv her",
        "gpadHint": "Forælder/værge underskriver her",
        "clear": "Slet og prøv igen",
        "agree": "Jeg har læst og forstået ovenstående, og jeg giver mit samtykke.",
        "send": "Underskriv og gem som PDF",
        "errName": "Skriv venligst dit navn.",
        "errUse": "Sæt kryds ved mindst ét sted, materialet må bruges.",
        "errSign": "Der mangler en underskrift.",
        "errAgree": "Sæt kryds i feltet om samtykke.",
        "errGuardianSign": "Der mangler en underskrift fra forælder eller værge.",
        "done": "Tak. PDF'en er gemt på denne enhed.\n\nSidder du med din egen telefon eller computer, så send den til skriv@sportsvaerkstedet.dk. Sidder du hos Sportsværkstedet, har vi den allerede.",
        "foot": "Denne fil virker uden internet. Intet af det, du skriver, sendes nogen steder hen - PDF'en gemmes kun på denne enhed.",
        "yes": "JA", "no": "NEJ",
        "pdfSigned": "Underskrevet digitalt", "pdfAt": "Tidspunkt",
        "pdfUses": "Samtykke givet til", "pdfPerson": "Underskriver",
        "pdfGuardian": "Forælder/værge",
    },
}

EN = {
    "lang": "en",
    "file": "Consent-ENGLISH.html",
    "pdfname": "Consent",
    "editheader": """  THE TEXT IS BELOW. You are meant to edit it.

  Only change what sits between the quotation marks "like this".
  Leave the commas, brackets and quotation marks exactly where they are and
  the file will keep working. Save it, then open it in a browser to check.

  Everything below "END OF THE TEXT" is the machinery. No reason to touch it.""",
    "editend": "END OF THE TEXT. Machinery below - leave it alone.",
    "t": {
        "title": "Consent to the use of film and photographs",
        "who": "Data controller: Sportsvaerkstedet, Domhusgade 13, 1st floor, 6000 Kolding, Denmark  ·  skriv@sportsvaerkstedet.dk",
        "intro": "Fill this in, tick what you agree to, and sign at the bottom.",
        "terms": [
            "I give Sportsvaerkstedet permission to film and photograph me during my treatment, and to use the material for marketing in the ways I have ticked below.",
            "I understand that the material may show my body, my injuries and the treatment itself, and that I may be recognisable.",
            "This consent is voluntary. I may withdraw it at any time by writing to Sportsvaerkstedet, who will then remove the material from their own channels as soon as possible. Material that others have already shared or downloaded cannot always be removed - I have been told this.",
            "I receive a copy of this declaration. Sportsvaerkstedet keeps it for as long as the material is in use, and for up to two years afterwards.",
            "I may ask to see, correct or delete my information, and I may complain to the Danish Data Protection Agency.",
        ],
        "hUses": "Where may the material be used?",
        "usesHelp": "Tick what you agree to. It is your choice - you may say yes to some and no to the rest.",
        "uses": [
            ["web", "The Sportsvaerkstedet website"],
            ["fb", "Facebook"],
            ["ig", "Instagram"],
            ["yt", "YouTube and TikTok"],
            ["ads", "Paid advertising (for example Facebook and Google ads)"],
            ["print", "Print - brochures, posters, signs"],
            ["other", "Any other marketing for Sportsvaerkstedet"],
            ["name", "My first name may be used alongside the material"],
        ],
        "hYou": "About you",
        "name": "Name", "birth": "Date of birth", "email": "Email",
        "phone": "Phone (optional)",
        "hGuardian": "Parent or guardian",
        "guardianWhy": "Only needed if the person being filmed is under 18. A parent or guardian signs alongside them.",
        "gname": "Parent/guardian name", "grel": "Relationship",
        "hSign": "Signature",
        "signHelp": "Sign with your finger or mouse in the box below.",
        "padHint": "Sign here",
        "gpadHint": "Parent/guardian signs here",
        "clear": "Clear and try again",
        "agree": "I have read and understood the above, and I give my consent.",
        "send": "Sign and save as PDF",
        "errName": "Please enter your name.",
        "errUse": "Please tick at least one place the material may be used.",
        "errSign": "The signature is missing.",
        "errAgree": "Please tick the consent box.",
        "errGuardianSign": "The parent or guardian's signature is missing.",
        "done": "Thank you. The PDF has been saved on this device.\n\nIf you are using your own phone or computer, please send it to skriv@sportsvaerkstedet.dk. If you are at Sportsvaerkstedet, we already have it.",
        "foot": "This file works without an internet connection. Nothing you type is sent anywhere - the PDF is saved on this device only.",
        "yes": "YES", "no": "NO",
        "pdfSigned": "Signed digitally", "pdfAt": "Time",
        "pdfUses": "Consent given for", "pdfPerson": "Signed by",
        "pdfGuardian": "Parent/guardian",
    },
}

TEMPLATE = """<!DOCTYPE html>
<html lang="__LANG__">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>__TITLE__</title>
<script>
/* ==========================================================================

__EDITHEADER__

   ========================================================================== */

const T = __TEXT__;

/* ==========================================================================
   __EDITEND__
   ========================================================================== */
</script>
<style>
  :root { color-scheme: light dark; --line:#cfd4da; --dim:#6b7280; --accent:#1a66d0;
          --bad:#c0392b; --ok:#1f7a4d; --panel:#ffffff; --bg:#f4f5f7; --text:#15181c; }
  @media (prefers-color-scheme: dark) {
    :root { --panel:#181b20; --bg:#0f1115; --text:#e7e9ec; --line:#333941; --dim:#98a0ab; }
  }
  * { box-sizing: border-box; }
  body { margin:0; background:var(--bg); color:var(--text);
         font:16px/1.6 -apple-system,"Segoe UI",Roboto,Arial,sans-serif; }
  .wrap { max-width:720px; margin:0 auto; padding:18px 16px 70px; }
  .card { background:var(--panel); border:1px solid var(--line); border-radius:10px;
          padding:18px 18px 20px; margin-bottom:16px; }
  h1 { font-size:22px; margin:0 0 4px; }
  h2 { font-size:15px; text-transform:uppercase; letter-spacing:.05em; color:var(--dim);
       margin:0 0 6px; }
  .who { color:var(--dim); font-size:14px; margin:0 0 14px; }
  .small { font-size:13.5px; color:var(--dim); margin:0 0 12px; }
  label.f { display:block; margin:0 0 12px; font-size:14px; color:var(--dim); }
  label.f input { display:block; width:100%; margin-top:4px; padding:11px 12px;
    font:inherit; color:var(--text); background:var(--panel);
    border:1px solid var(--line); border-radius:7px; }
  label.f input:focus { outline:2px solid var(--accent); outline-offset:-1px; }
  .two { display:flex; gap:12px; flex-wrap:wrap; }
  .two > * { flex:1 1 220px; }
  .terms p { margin:0 0 10px; font-size:15px; }
  .tick { display:flex; gap:10px; align-items:flex-start; margin:0 0 9px;
          padding:9px 11px; border:1px solid var(--line); border-radius:8px; }
  .tick input { margin-top:3px; width:19px; height:19px; flex:0 0 19px; }
  .tick span { font-size:15px; }
  canvas.pad { border:1px dashed var(--line); border-radius:8px; background:var(--panel);
         touch-action:none; width:100%; height:170px; display:block; }
  .padrow { display:flex; justify-content:space-between; align-items:center;
            gap:10px; margin:6px 0 14px; font-size:13px; color:var(--dim); }
  button { font:inherit; padding:9px 15px; border-radius:8px; border:1px solid var(--line);
           background:var(--panel); color:var(--text); cursor:pointer; }
  button.primary { background:var(--accent); border-color:var(--accent); color:#fff;
                   font-weight:600; padding:14px 20px; font-size:17px; width:100%; }
  button:disabled { opacity:.5; cursor:default; }
  .err { color:var(--bad); font-size:14px; margin:10px 0 0; }
  .done { border-left:4px solid var(--ok); background:#eef8f2; color:#14532d;
          padding:14px 16px; border-radius:6px; white-space:pre-line; }
  @media (prefers-color-scheme: dark) { .done { background:#10241a; color:#b7e4c7; } }
</style>
</head>
<body>
<div class="wrap">

  <div class="card">
    <h1 id="title"></h1>
    <p class="who" id="who"></p>
    <p class="small" id="intro"></p>
    <div class="terms" id="terms"></div>
  </div>

  <div class="card">
    <h2 id="hUses"></h2>
    <p class="small" id="usesHelp"></p>
    <div id="uses"></div>
  </div>

  <div class="card">
    <h2 id="hYou"></h2>
    <div class="two">
      <label class="f" id="lName"> <input type="text" id="name" autocomplete="name"></label>
      <label class="f" id="lBirth"><input type="date" id="birth"></label>
    </div>
    <div class="two">
      <label class="f" id="lEmail"><input type="email" id="email" autocomplete="email"></label>
      <label class="f" id="lPhone"><input type="tel" id="phone" autocomplete="tel"></label>
    </div>
  </div>

  <div class="card">
    <h2 id="hSign"></h2>
    <p class="small" id="signHelp"></p>
    <canvas class="pad" id="pad"></canvas>
    <div class="padrow">
      <span id="padHint"></span>
      <button type="button" id="clear"></button>
    </div>
    <label class="tick">
      <input type="checkbox" id="agree">
      <span id="agreeText"></span>
    </label>
  </div>

  <div class="card">
    <h2 id="hGuardian"></h2>
    <p class="small" id="guardianWhy"></p>
    <div class="two">
      <label class="f" id="lGName"><input type="text" id="gname"></label>
      <label class="f" id="lGRel"> <input type="text" id="grel"></label>
    </div>
    <canvas class="pad" id="gpad"></canvas>
    <div class="padrow">
      <span id="gpadHint"></span>
      <button type="button" id="gclear"></button>
    </div>
  </div>

  <div class="card">
    <p class="err" id="err" hidden></p>
    <button class="primary" id="send"></button>
    <div id="done" class="done" hidden style="margin-top:14px"></div>
  </div>

  <p class="small" id="foot"></p>
</div>

<script>__JSPDF__</script>
<script>
const $ = id => document.getElementById(id);
const PDFNAME = "__PDFNAME__";

function fill() {
  $("title").textContent = T.title;
  $("who").textContent = T.who;
  $("intro").textContent = T.intro;
  T.terms.forEach(p => { const e = document.createElement("p"); e.textContent = p; $("terms").appendChild(e); });

  $("hUses").textContent = T.hUses;
  $("usesHelp").textContent = T.usesHelp;
  T.uses.forEach(([key, label]) => {
    const wrap = document.createElement("label");
    wrap.className = "tick";
    const box = document.createElement("input");
    box.type = "checkbox"; box.dataset.key = key;
    const span = document.createElement("span");
    span.textContent = label;
    wrap.append(box, span);
    $("uses").appendChild(wrap);
  });

  const put = (id, text) => { $(id).childNodes[0].nodeValue = text + " "; };
  $("hYou").textContent = T.hYou;
  put("lName", T.name); put("lBirth", T.birth); put("lEmail", T.email); put("lPhone", T.phone);
  $("hGuardian").textContent = T.hGuardian;
  $("guardianWhy").textContent = T.guardianWhy;
  put("lGName", T.gname); put("lGRel", T.grel);

  $("hSign").textContent = T.hSign;
  $("signHelp").textContent = T.signHelp;
  $("padHint").textContent = T.padHint;
  $("gpadHint").textContent = T.gpadHint;
  $("clear").textContent = T.clear;
  $("gclear").textContent = T.clear;
  $("agreeText").textContent = T.agree;
  $("send").textContent = T.send;
  $("foot").textContent = T.foot;
  document.title = T.title;
}

// -------------------------------------------------------- signature pads
function makePad(canvas) {
  const ctx = canvas.getContext("2d");
  const state = { inked: false };
  let drawing = false, last = null;

  function size() {
    // Draw at the device's real resolution, or a finger-drawn line lands in
    // the PDF as a blurry staircase.
    const ratio = window.devicePixelRatio || 1;
    const box = canvas.getBoundingClientRect();
    const keep = state.inked ? canvas.toDataURL() : null;
    canvas.width = Math.round(box.width * ratio);
    canvas.height = Math.round(box.height * ratio);
    ctx.scale(ratio, ratio);
    ctx.lineWidth = 2.2; ctx.lineCap = "round"; ctx.lineJoin = "round";
    ctx.strokeStyle = "#111827";
    if (keep) { const i = new Image(); i.onload = () => ctx.drawImage(i, 0, 0, box.width, box.height); i.src = keep; }
  }

  const at = ev => {
    const box = canvas.getBoundingClientRect();
    return { x: ev.clientX - box.left, y: ev.clientY - box.top };
  };

  canvas.addEventListener("pointerdown", ev => {
    ev.preventDefault();
    canvas.setPointerCapture(ev.pointerId);
    drawing = true; last = at(ev); state.inked = true;
    ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(last.x + 0.1, last.y); ctx.stroke();
  });
  canvas.addEventListener("pointermove", ev => {
    if (!drawing) return;
    ev.preventDefault();
    const p = at(ev);
    ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(p.x, p.y); ctx.stroke();
    last = p;
  });
  ["pointerup", "pointercancel", "pointerleave"].forEach(e =>
    canvas.addEventListener(e, () => { drawing = false; }));

  window.addEventListener("resize", size);
  state.size = size;
  state.clear = () => { ctx.clearRect(0, 0, canvas.width, canvas.height); state.inked = false; };
  state.data = () => canvas.toDataURL("image/png");
  return state;
}

const sig = makePad($("pad"));
const gsig = makePad($("gpad"));
$("clear").onclick = () => sig.clear();
$("gclear").onclick = () => gsig.clear();

// ------------------------------------------------------------------ save
function stamp() {
  const d = new Date(), p = n => String(n).padStart(2, "0");
  return p(d.getDate()) + "-" + p(d.getMonth() + 1) + "-" + d.getFullYear() +
         " " + p(d.getHours()) + ":" + p(d.getMinutes());
}

$("send").onclick = () => {
  const name = $("name").value.trim();
  const boxes = [...document.querySelectorAll("#uses input")];
  const anyUse = boxes.some(b => b.checked && b.dataset.key !== "name");
  const guardianStarted = $("gname").value.trim() !== "" || gsig.inked;

  let problem = "";
  if (!name) problem = T.errName;
  else if (!anyUse) problem = T.errUse;
  else if (!sig.inked) problem = T.errSign;
  else if (!$("agree").checked) problem = T.errAgree;
  else if (guardianStarted && !gsig.inked) problem = T.errGuardianSign;

  const err = $("err");
  if (problem) { err.textContent = problem; err.hidden = false; err.scrollIntoView({block:"nearest"}); return; }
  err.hidden = true;

  const when = stamp();
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: "mm", format: "a4" });
  const M = 18, W = 210 - M * 2;
  let y = M;
  const room = need => { if (y + need > 278) { doc.addPage(); y = M; } };

  doc.setFont("helvetica", "bold"); doc.setFontSize(15);
  doc.text(T.title, M, y); y += 7;
  doc.setFont("helvetica", "normal"); doc.setFontSize(9); doc.setTextColor(90);
  doc.splitTextToSize(T.who, W).forEach(l => { doc.text(l, M, y); y += 4.4; });
  y += 4; doc.setTextColor(20); doc.setFontSize(10);

  T.terms.forEach(p => {
    doc.splitTextToSize(p, W).forEach(l => { room(6); doc.text(l, M, y); y += 4.8; });
    y += 2.5;
  });

  y += 3; room(12);
  doc.setFont("helvetica", "bold"); doc.text(T.pdfUses + ":", M, y); y += 5.5;
  doc.setFont("helvetica", "normal");
  boxes.forEach(b => {
    room(6);
    doc.text((b.checked ? "[x] " : "[ ] ") + b.nextElementSibling.textContent +
             "  -  " + (b.checked ? T.yes : T.no), M, y);
    y += 5;
  });

  y += 4; room(30);
  doc.setFont("helvetica", "bold"); doc.text(T.pdfPerson + ":", M, y); y += 5.5;
  doc.setFont("helvetica", "normal");
  [T.name + ": " + name,
   T.birth + ": " + ($("birth").value || "-"),
   T.email + ": " + ($("email").value.trim() || "-"),
   T.phone + ": " + ($("phone").value.trim() || "-")
  ].forEach(l => { room(6); doc.text(l, M, y); y += 5; });

  y += 3; room(48);
  doc.setFont("helvetica", "bold"); doc.text(T.hSign + ":", M, y); y += 4;
  doc.setFont("helvetica", "normal");
  doc.addImage(sig.data(), "PNG", M, y, 78, 25); y += 27;
  doc.setDrawColor(150); doc.line(M, y, M + 78, y); y += 4.6;
  doc.setFontSize(9); doc.setTextColor(90);
  doc.text(name, M, y); y += 4.4;
  doc.text(T.pdfSigned + "  ·  " + T.pdfAt + ": " + when, M, y); y += 8;
  doc.setTextColor(20); doc.setFontSize(10);

  if (gsig.inked || $("gname").value.trim()) {
    room(46);
    doc.setFont("helvetica", "bold"); doc.text(T.pdfGuardian + ":", M, y); y += 5.5;
    doc.setFont("helvetica", "normal");
    doc.text(T.gname + ": " + ($("gname").value.trim() || "-"), M, y); y += 5;
    doc.text(T.grel + ": " + ($("grel").value.trim() || "-"), M, y); y += 5;
    if (gsig.inked) {
      doc.addImage(gsig.data(), "PNG", M, y, 78, 25); y += 27;
      doc.setDrawColor(150); doc.line(M, y, M + 78, y); y += 4.6;
      doc.setFontSize(9); doc.setTextColor(90);
      doc.text(($("gname").value.trim() || "-"), M, y); y += 4.4;
      doc.text(T.pdfSigned + "  ·  " + T.pdfAt + ": " + when, M, y);
    }
  }

  const safe = name.replace(/[^\\p{L}\\p{N} _-]/gu, "").trim().replace(/\\s+/g, "-") || "x";
  doc.save(PDFNAME + "-" + safe + ".pdf");

  $("done").textContent = T.done;
  $("done").hidden = false;
  $("send").disabled = true;
  $("done").scrollIntoView({ block: "nearest" });
};

fill();
sig.size();
gsig.size();
</script>
</body>
</html>
"""


def build(spec):
    text = json.dumps(spec["t"], ensure_ascii=False, indent=2)
    html = (TEMPLATE
            .replace("__LANG__", spec["lang"])
            .replace("__TITLE__", spec["t"]["title"])
            .replace("__EDITHEADER__", spec["editheader"])
            .replace("__EDITEND__", spec["editend"])
            .replace("__TEXT__", text)
            .replace("__PDFNAME__", spec["pdfname"])
            .replace("__JSPDF__", JSPDF))

    out = os.path.join(HERE, "dist", spec["file"])
    os.makedirs(os.path.dirname(out), exist_ok=True)
    with io.open(out, "w", encoding="utf-8") as h:
        h.write(html)
    print("%-26s %6.0f KB" % (spec["file"], os.path.getsize(out) / 1024))


for spec in (DA, EN):
    build(spec)
